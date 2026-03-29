<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoryGroup;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            [
                'name' => 'Brand Awareness',
                'slug' => 'brand-awareness',
                'sort_order' => 1,
                'categories' => [
                    ['name' => 'OOH',          'subtitle' => 'Billboards & Hoardings',   'media_type_filter' => 'billboard',  'sort_order' => 1, 'image_url' => 'https://images.unsplash.com/photo-1516912481808-3406841bd33c?w=600&h=400&fit=crop&q=80'],
                    ['name' => 'BTL',          'subtitle' => 'Below the Line Marketing', 'media_type_filter' => 'btl',        'sort_order' => 2, 'image_url' => 'https://images.unsplash.com/photo-1485182708500-e8f1f318ba72?w=600&h=400&fit=crop&q=80'],
                    ['name' => 'Print Media',  'subtitle' => 'Newspapers & Magazines',   'media_type_filter' => 'print',      'sort_order' => 3, 'image_url' => 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=600&h=400&fit=crop&q=80'],
                    ['name' => 'Mall',         'subtitle' => 'Retail & Kiosk Activations','media_type_filter' => 'mall',      'sort_order' => 4, 'image_url' => 'https://images.unsplash.com/photo-1519567241046-7f570eee3ce6?w=600&h=400&fit=crop&q=80'],
                    ['name' => 'Airport',      'subtitle' => 'Digital & Terminal Ads',   'media_type_filter' => 'airport',    'sort_order' => 5, 'image_url' => 'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?w=600&h=400&fit=crop&q=80'],
                ],
            ],
            [
                'name' => 'Digital Marketing',
                'slug' => 'digital-marketing',
                'sort_order' => 2,
                'categories' => [
                    ['name' => 'Digital Marketing',  'subtitle' => 'Search, Display & Programmatic', 'media_type_filter' => 'digital',  'sort_order' => 1, 'image_url' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&h=400&fit=crop&q=80'],
                    ['name' => 'In App Advertising', 'subtitle' => 'Mobile Apps & Games',            'media_type_filter' => 'app',       'sort_order' => 2, 'image_url' => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=600&h=400&fit=crop&q=80'],
                    ['name' => 'Influencers',        'subtitle' => 'Creator Campaigns',              'media_type_filter' => 'influencer','sort_order' => 3, 'image_url' => 'https://images.unsplash.com/photo-1611162617213-7d7a39e9b1d7?w=600&h=400&fit=crop&q=80'],
                    ['name' => 'Celebrity',          'subtitle' => 'Celebrity Brand Endorsements',   'media_type_filter' => 'celebrity', 'sort_order' => 4, 'image_url' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?w=600&h=400&fit=crop&q=80'],
                ],
            ],
            [
                'name' => 'Entertainment & Events',
                'slug' => 'entertainment-events',
                'sort_order' => 3,
                'categories' => [
                    ['name' => 'Cinema',           'subtitle' => 'Movies & Multiplexes',    'media_type_filter' => 'cinema',    'sort_order' => 1, 'image_url' => 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=600&h=400&fit=crop&q=80'],
                    ['name' => 'Sports / IPL',     'subtitle' => 'Stadiums & Live Events',  'media_type_filter' => 'sports',    'sort_order' => 2, 'image_url' => 'https://images.unsplash.com/photo-1540747913346-19e32dc3e97e?w=600&h=400&fit=crop&q=80'],
                    ['name' => 'Television',       'subtitle' => 'TV Channels & OTT',       'media_type_filter' => 'tv',        'sort_order' => 3, 'image_url' => 'https://images.unsplash.com/photo-1593359677879-a4bb92f4834c?w=600&h=400&fit=crop&q=80'],
                    ['name' => 'Radio',            'subtitle' => 'FM Channels & Podcasts',  'media_type_filter' => 'radio',     'sort_order' => 4, 'image_url' => 'https://images.unsplash.com/photo-1478737270239-2f02b77fc618?w=600&h=400&fit=crop&q=80'],
                    ['name' => 'In Film Branding', 'subtitle' => 'Product Placement & Co-branding', 'media_type_filter' => 'film', 'sort_order' => 5, 'image_url' => 'https://images.unsplash.com/photo-1485846234645-a62644f84728?w=600&h=400&fit=crop&q=80'],
                    ['name' => 'Event Marketing',  'subtitle' => 'Concerts, Expos & Live',  'media_type_filter' => 'event',     'sort_order' => 6, 'image_url' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=600&h=400&fit=crop&q=80'],
                ],
            ],
        ];

        foreach ($groups as $gData) {
            $cats = $gData['categories'];
            unset($gData['categories']);

            $group = CategoryGroup::updateOrCreate(
                ['slug' => $gData['slug']],
                $gData
            );

            foreach ($cats as $catData) {
                Category::updateOrCreate(
                    ['category_group_id' => $group->id, 'name' => $catData['name']],
                    array_merge($catData, ['category_group_id' => $group->id])
                );
            }
        }
    }
}
