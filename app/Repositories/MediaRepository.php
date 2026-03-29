<?php

namespace App\Repositories;

use App\Models\Media;

class MediaRepository
{
    public function create(array $data)
    {
        return Media::create($data);
    }

    public function findById($id)
    {
        return Media::find($id);
    }

    public function update($id, array $data)
    {
        $media = $this->findById($id);
        if ($media) {
            $media->update($data);
            return $media;
        }
        return null;
    }

    public function delete($id)
    {
        $media = $this->findById($id);
        if ($media) {
            $media->delete();
            return true;
        }
        return false;
    }

    public function getFiltered(array $filters)
    {
        $query = Media::with('images');

        if (isset($filters['city'])) {
            $query->where('city', $filters['city']);
        }
        if (isset($filters['type'])) {
            $query->where('media_type', $filters['type']);
        }
        if (isset($filters['min_price'])) {
            $query->where('base_price', '>=', $filters['min_price']);
        }
        if (isset($filters['max_price'])) {
            $query->where('base_price', '<=', $filters['max_price']);
        }
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->get();
    }
}
