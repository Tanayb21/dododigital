<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    /**
     * GET /api/banners
     * Public: return active banners ordered by sort_order.
     */
    public function publicIndex()
    {
        $banners = Banner::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json($banners);
    }

    /**
     * GET /api/admin/banners
     * Admin: return ALL banners.
     */
    public function adminIndex()
    {
        return response()->json(
            Banner::orderBy('sort_order')->get()
        );
    }

    /**
     * POST /api/admin/banners
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'            => 'nullable|string|max:255',
            'subtitle'         => 'nullable|string|max:500',
            'image_url'        => 'required|string|max:1000',
            'mobile_image_url' => 'nullable|string|max:1000',
            'link_url'         => 'nullable|string|max:1000',
            'button_text'      => 'nullable|string|max:100',
            'bg_color'         => 'nullable|string|max:20',
            'text_color'       => 'nullable|string|max:20',
            'size'             => 'nullable|in:hero,promo',
            'sort_order'       => 'nullable|integer',
            'is_active'        => 'nullable|boolean',
        ]);

        $banner = Banner::create($data);

        return response()->json([
            'message' => 'Banner created.', 'banner' => $banner
        ], 201);
    }

    /**
     * PATCH /api/admin/banners/{id}
     */
    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $data = $request->validate([
            'title'            => 'nullable|string|max:255',
            'subtitle'         => 'nullable|string|max:500',
            'image_url'        => 'sometimes|string|max:1000',
            'mobile_image_url' => 'nullable|string|max:1000',
            'link_url'         => 'nullable|string|max:1000',
            'button_text'      => 'nullable|string|max:100',
            'bg_color'         => 'nullable|string|max:20',
            'text_color'       => 'nullable|string|max:20',
            'size'             => 'nullable|in:hero,promo',
            'sort_order'       => 'nullable|integer',
            'is_active'        => 'nullable|boolean',
        ]);

        $banner->update($data);

        return response()->json([
            'message' => 'Banner updated.', 'banner' => $banner
        ]);
    }

    /**
     * DELETE /api/admin/banners/{id}
     */
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->delete();

        return response()->json(['message' => 'Banner deleted.']);
    }
}
