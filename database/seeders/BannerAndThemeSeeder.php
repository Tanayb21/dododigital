<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerAndThemeSeeder extends Seeder
{
    public function run(): void
    {
        // ── Theme Color Settings ─────────────────────────────────────
        $themeSettings = [
            ['group' => 'theme', 'key' => 'theme_primary',       'label' => 'Primary Color',       'value' => '#0d9488', 'type' => 'color', 'is_secret' => false, 'sort_order' => 1],
            ['group' => 'theme', 'key' => 'theme_primary_dark',  'label' => 'Primary Dark',        'value' => '#0f766e', 'type' => 'color', 'is_secret' => false, 'sort_order' => 2],
            ['group' => 'theme', 'key' => 'theme_primary_light', 'label' => 'Primary Light/BG',    'value' => '#e0f2f1', 'type' => 'color', 'is_secret' => false, 'sort_order' => 3],
            ['group' => 'theme', 'key' => 'theme_accent',        'label' => 'Accent Color',        'value' => '#06b6d4', 'type' => 'color', 'is_secret' => false, 'sort_order' => 4],
            ['group' => 'theme', 'key' => 'theme_text',          'label' => 'Text Color',          'value' => '#111827', 'type' => 'color', 'is_secret' => false, 'sort_order' => 5],
            ['group' => 'theme', 'key' => 'theme_bg',            'label' => 'Background Color',    'value' => '#f9fafb', 'type' => 'color', 'is_secret' => false, 'sort_order' => 6],
        ];

        foreach ($themeSettings as $s) {
            Setting::updateOrCreate(['key' => $s['key']], $s);
        }

        // ── Demo Banners ─────────────────────────────────────────────
        $banners = [
            [
                'title'       => 'Book Premium Ad Space',
                'subtitle'    => 'Billboards, hoardings, LED screens across India — reserve and go live within 24 hours.',
                'image_url'   => 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=1200&h=400&fit=crop&q=80',
                'link_url'    => '/media',
                'button_text' => 'Explore Now',
                'bg_color'    => '#0d9488',
                'text_color'  => '#ffffff',
                'size'        => 'hero',
                'sort_order'  => 1,
                'is_active'   => true,
            ],
            [
                'title'       => 'Airport Advertising',
                'subtitle'    => 'Reach millions of travellers with airport display ads.',
                'image_url'   => 'https://images.unsplash.com/photo-1436491865332-7a61a109db05?w=600&h=300&fit=crop&q=80',
                'link_url'    => '/media?type=airport',
                'button_text' => 'View Options',
                'bg_color'    => '#f59e0b',
                'text_color'  => '#ffffff',
                'size'        => 'promo',
                'sort_order'  => 2,
                'is_active'   => true,
            ],
            [
                'title'       => 'Cinema Advertising',
                'subtitle'    => 'In-film branding, pre-show ad slots, and lobby displays.',
                'image_url'   => 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=600&h=300&fit=crop&q=80',
                'link_url'    => '/media?type=cinema',
                'button_text' => 'Book Now',
                'bg_color'    => '#8b5cf6',
                'text_color'  => '#ffffff',
                'size'        => 'promo',
                'sort_order'  => 3,
                'is_active'   => true,
            ],
            [
                'title'       => 'Mall Branding',
                'subtitle'    => 'Get your brand in front of thousands of shoppers daily.',
                'image_url'   => 'https://images.unsplash.com/photo-1519566335946-e6f65f0f4fdf?w=600&h=300&fit=crop&q=80',
                'link_url'    => '/media?type=mall',
                'button_text' => 'Explore',
                'bg_color'    => '#ec4899',
                'text_color'  => '#ffffff',
                'size'        => 'promo',
                'sort_order'  => 4,
                'is_active'   => true,
            ],
        ];

        foreach ($banners as $b) {
            Banner::updateOrCreate(
                ['title' => $b['title'], 'size' => $b['size']],
                $b
            );
        }
    }
}
