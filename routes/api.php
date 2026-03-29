<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BannerController;

// Public settings (branding, app name — no secrets)
Route::get('/settings/public', [SettingController::class, 'publicSettings']);

// Public categories (for homepage)
Route::get('/categories', [CategoryController::class, 'publicIndex']);

// Public banners (for homepage)
Route::get('/banners', [BannerController::class, 'publicIndex']);

// ─── Public Routes ──────────────────────────────────────────────────────────

// Public media listing with filters
Route::get('/media', [MediaController::class, 'publicList']);
Route::get('/media/{id}', [MediaController::class, 'show']);

// ─── Authenticated User Routes ───────────────────────────────────────────────

Route::middleware(['auth:sanctum'])->group(function () {

    // Current user info
    Route::get('/user', fn(Request $request) => $request->user());

    // Vendor registration & profile
    Route::post('/vendor/register',  [VendorController::class, 'register']);
    Route::get('/vendor/profile',    [VendorController::class, 'profile']);

    // Vendor media CRUD (auth + must be approved vendor - checked inside controller)
    Route::get('/vendor/media',           [MediaController::class, 'index']);
    Route::post('/vendor/media',          [MediaController::class, 'store']);
    Route::put('/vendor/media/{id}',      [MediaController::class, 'update']);
    Route::delete('/vendor/media/{id}',   [MediaController::class, 'destroy']);

    // ─── Booking Routes ──────────────────────────────────────────────────────
    Route::get('/bookings/preview',        [BookingController::class, 'preview']);
    Route::get('/bookings',                [BookingController::class, 'index']);
    Route::post('/bookings',               [BookingController::class, 'store']);
    Route::get('/bookings/{id}',           [BookingController::class, 'show']);
    Route::delete('/bookings/{id}/cancel', [BookingController::class, 'cancel']);

    // ─── Payment Routes ───────────────────────────────────────────────────────
    Route::post('/payments/create-order',          [PaymentController::class, 'createOrder']);
    Route::post('/payments/verify',                [PaymentController::class, 'verify']);
    Route::post('/payments/failed',                [PaymentController::class, 'failed']);
    Route::get('/payments/{bookingId}',            [PaymentController::class, 'show']);
    Route::get('/payments/{bookingId}/invoice',    [PaymentController::class, 'downloadInvoice']);

    // ─── Vendor Booking Management ────────────────────────────────────────────
    Route::get('/vendor/bookings',                        [VendorController::class, 'myBookings']);
    Route::patch('/vendor/bookings/{id}/quote-price',     [VendorController::class, 'quotePrice']);
});

// ─── Admin-Only Routes ───────────────────────────────────────────────────────

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {

    // Vendor management
    Route::get('/vendors',                    [AdminController::class, 'listVendors']);
    Route::patch('/vendor/{id}/approve',      [AdminController::class, 'approveVendor']);
    Route::patch('/vendor/{id}/reject',       [AdminController::class, 'rejectVendor']);

    // Media moderation
    Route::patch('/media/{id}/status',        [AdminController::class, 'setMediaStatus']);

    // Secret login as vendor
    Route::post('/login-as/{userId}',         [AdminController::class, 'loginAs']);

    // Booking management
    Route::get('/bookings',                        [AdminController::class, 'listBookings']);
    Route::patch('/bookings/{id}/status',          [AdminController::class, 'updateBookingStatus']);

    // ─── Settings Management ───────────────────────────────────────────────
    Route::get('/settings',           [SettingController::class, 'index']);
    Route::get('/settings/{group}',   [SettingController::class, 'group']);
    Route::post('/settings',          [SettingController::class, 'update']);

    // ─── Category Management ──────────────────────────────────────────────
    Route::get('/categories',                  [CategoryController::class, 'adminIndex']);
    Route::post('/categories/groups',          [CategoryController::class, 'storeGroup']);
    Route::patch('/categories/groups/{id}',    [CategoryController::class, 'updateGroup']);
    Route::delete('/categories/groups/{id}',   [CategoryController::class, 'destroyGroup']);
    Route::post('/categories',                 [CategoryController::class, 'storeCategory']);
    Route::patch('/categories/{id}',           [CategoryController::class, 'updateCategory']);
    Route::delete('/categories/{id}',          [CategoryController::class, 'destroyCategory']);

    // ─── Banner Management ─────────────────────────────────────────────
    Route::get('/banners',              [BannerController::class, 'adminIndex']);
    Route::post('/banners',             [BannerController::class, 'store']);
    Route::patch('/banners/{id}',       [BannerController::class, 'update']);
    Route::delete('/banners/{id}',      [BannerController::class, 'destroy']);
});

