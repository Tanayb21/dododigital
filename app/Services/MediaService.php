<?php

namespace App\Services;

use App\Repositories\MediaRepository;
use App\Models\MediaImage;

class MediaService
{
    protected $mediaRepo;

    public function __construct(MediaRepository $mediaRepo)
    {
        $this->mediaRepo = $mediaRepo;
    }

    public function createMedia(array $data, array $imagePaths)
    {
        if (empty($imagePaths)) {
            throw new \Exception('Media must have at least 1 image.');
        }
        if ($data['base_price'] <= 0) {
            throw new \Exception('Price must be greater than 0.');
        }

        $data['status'] = 'active'; // In production, this would be 'inactive' for moderation
        
        $media = $this->mediaRepo->create($data);

        foreach ($imagePaths as $path) {
            MediaImage::create([
                'media_id' => $media->id,
                'image_url' => $path
            ]);
        }

        return $media;
    }

    public function updateMedia($id, array $data)
    {
        return $this->mediaRepo->update($id, $data);
    }

    public function deleteMedia($id)
    {
        return $this->mediaRepo->delete($id);
    }

    public function setStatus($id, $status)
    {
        return $this->mediaRepo->update($id, ['status' => $status]);
    }

    public function getFilteredMedia(array $filters)
    {
        return $this->mediaRepo->getFiltered($filters);
    }
}
