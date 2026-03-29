<?php

namespace App\Repositories;

use App\Models\Vendor;

class VendorRepository
{
    public function create(array $data)
    {
        return Vendor::create($data);
    }

    public function findByUserId($userId)
    {
        return Vendor::where('user_id', $userId)->first();
    }

    public function findById($id)
    {
        return Vendor::find($id);
    }

    public function updateStatus($id, $status)
    {
        $vendor = $this->findById($id);
        if ($vendor) {
            $vendor->update(['status' => $status]);
            return $vendor;
        }
        return null;
    }
}
