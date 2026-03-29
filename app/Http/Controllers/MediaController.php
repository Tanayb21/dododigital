<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    /**
     * GET /vendor/media
     * List media for the authenticated vendor.
     */
    public function index(Request $request)
    {
        $vendor = $request->user()->vendor;

        if (!$vendor) {
            return response()->json(['message' => 'Vendor profile not found.'], 404);
        }

        return response()->json(
            $vendor->media()->with('images')->get()
        );
    }

    /**
     * POST /vendor/media
     * Create a new media listing. Vendor must be approved.
     */
    public function store(Request $request)
    {
        $vendor = $request->user()->vendor;

        if (!$vendor || $vendor->status !== 'approved') {
            return response()->json(['message' => 'Your vendor account must be approved before adding media.'], 403);
        }

        $request->validate([
            'title'          => 'required|string|max:255',
            'media_type'     => 'required|string',
            'city'           => 'required|string',
            'location'       => 'required|string',
            'price_on_call'  => 'sometimes|boolean',
            'base_price'     => 'required_if:price_on_call,false|nullable|numeric|min:0.01',
            'pricing_type'   => 'required|in:time,unit,cpm',
            'latitude'       => 'nullable|numeric',
            'longitude'      => 'nullable|numeric',
            'size'           => 'nullable|string',
            'description'    => 'nullable|string',
            'images'         => 'required|array|min:1',
            'images.*'       => 'required|image|max:5120',
        ]);

        $imagePaths = [];
        foreach ($request->file('images') as $image) {
            $path = $image->store('media', 'public');
            $imagePaths[] = $path;
        }

        try {
            $data = array_merge(
                $request->only(
                    'title', 'media_type', 'city', 'location',
                    'base_price', 'price_on_call', 'pricing_type',
                    'latitude', 'longitude', 'size', 'description'
                ),
                ['vendor_id' => $vendor->id]
            );

            // If price_on_call is true, base_price can be null/0
            if (!empty($data['price_on_call'])) {
                $data['base_price'] = 0;
            }

            $media = $this->mediaService->createMedia($data, $imagePaths);

            return response()->json([
                'message' => 'Media created successfully.',
                'media'   => $media->load('images'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * PUT /vendor/media/{id}
     * Update a media listing (only for the owner vendor).
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $vendor = $user->vendor;
        $media  = Media::find($id);

        if (!$media) {
            return response()->json(['message' => 'Media not found.'], 404);
        }

        // Admin can edit anything. Vendors can only edit their own.
        if ($user->role !== 'admin' && (!$vendor || $media->vendor_id !== $vendor->id)) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'title'          => 'sometimes|string|max:255',
            'media_type'     => 'sometimes|string',
            'city'           => 'sometimes|string',
            'location'       => 'sometimes|string',
            'price_on_call'  => 'sometimes|boolean',
            'base_price'     => 'sometimes|nullable|numeric|min:0.01',
            'pricing_type'   => 'sometimes|in:time,unit,cpm',
            'latitude'       => 'nullable|numeric',
            'longitude'      => 'nullable|numeric',
            'size'           => 'nullable|string',
            'description'    => 'nullable|string',
        ]);

        $data = $request->only(
            'title', 'media_type', 'city', 'location',
            'base_price', 'price_on_call', 'pricing_type',
            'latitude', 'longitude', 'size', 'description'
        );

        if (isset($data['price_on_call']) && $data['price_on_call']) {
            $data['base_price'] = 0;
        }

        $media = $this->mediaService->updateMedia($id, $data);

        return response()->json([
            'message' => 'Media updated successfully.',
            'media'   => $media->load('images'),
        ]);
    }

    /**
     * DELETE /vendor/media/{id}
     * Delete a media listing (only for the owner vendor).
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $vendor = $user->vendor;
        $media  = Media::find($id);

        if (!$media) {
            return response()->json(['message' => 'Media not found.'], 404);
        }

        // Admin can delete anything. Vendors can only delete their own.
        if ($user->role !== 'admin' && (!$vendor || $media->vendor_id !== $vendor->id)) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $this->mediaService->deleteMedia($id);

        return response()->json(['message' => 'Media deleted successfully.']);
    }

    /**
     * GET /media/{id}
     * Get details for a single media listing.
     */
    public function show($id)
    {
        $media = Media::with(['images', 'vendor'])->findOrFail($id);
        return response()->json($media);
    }

    /**
     * GET /media
     * Public listing with filters: city, type, min_price, max_price.
     */
    public function publicList(Request $request)
    {
        $filters = array_filter([
            'city'      => $request->query('city'),
            'type'      => $request->query('type'),
            'min_price' => $request->query('min_price'),
            'max_price' => $request->query('max_price'),
            'status'    => 'active',  // Only show active media publicly
        ]);

        $media = $this->mediaService->getFilteredMedia($filters);

        return response()->json($media);
    }
}
