<?php

namespace App\Services;

use App\Repositories\VendorRepository;
use Illuminate\Support\Facades\DB;

class VendorService
{
    protected $vendorRepo;

    public function __construct(VendorRepository $vendorRepo)
    {
        $this->vendorRepo = $vendorRepo;
    }

    public function registerVendor($userId, array $data)
    {
        // One vendor per user
        $existing = $this->vendorRepo->findByUserId($userId);
        if ($existing) {
            throw new \Exception('Vendor profile already exists for this user.');
        }

        $data['user_id'] = $userId;
        $data['status'] = 'pending'; // Requires admin approval
        
        return $this->vendorRepo->create($data);
    }

    public function approveVendor($id)
    {
        return $this->vendorRepo->updateStatus($id, 'approved');
    }

    public function rejectVendor($id)
    {
        return $this->vendorRepo->updateStatus($id, 'rejected');
    }
}
