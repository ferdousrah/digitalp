<?php

namespace Database\Seeders;

use App\Models\HomeSection;
use Illuminate\Database\Seeder;

class HomeSectionSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'is_enabled'      => true,
            'heading_color'   => '#111827',
            'subheading_color'=> '#6b7280',
            'text_align'      => 'center',
            'extra'           => ['show_divider' => true, 'content_align' => 'left'],
        ];

        $sections = [
            [
                'key'              => 'shop_by_category',
                'type'             => 'category',
                'title'            => 'Shop by Category',
                'subtitle'         => 'Browse our wide selection by category',
                'sort_order'       => 1,
                'items_count'      => 8,
                'display_type'     => 'carousel',
                'desktop_visible'  => 6,
                'mobile_visible'   => 2,
            ],
            [
                'key'             => 'featured_products',
                'type'            => 'product',
                'title'           => 'Featured Products',
                'subtitle'        => 'Hand-picked products just for you',
                'sort_order'      => 2,
                'items_count'     => 8,
                'desktop_columns' => 4,
                'mobile_columns'  => 2,
                'rows'            => 2,
                'extra'           => ['product_filter' => 'featured'],
            ],
            [
                'key'             => 'new_arrivals',
                'type'            => 'product',
                'title'           => 'New Arrivals',
                'subtitle'        => 'The latest additions to our store',
                'sort_order'      => 3,
                'items_count'     => 8,
                'desktop_columns' => 4,
                'mobile_columns'  => 2,
                'rows'            => 2,
                'extra'           => ['product_filter' => 'new_arrival'],
            ],
            [
                'key'             => 'best_sellers',
                'type'            => 'product',
                'title'           => 'Best Sellers',
                'subtitle'        => 'Our most popular products',
                'sort_order'      => 4,
                'items_count'     => 8,
                'desktop_columns' => 4,
                'mobile_columns'  => 2,
                'rows'            => 2,
                'extra'           => ['product_filter' => 'best_seller'],
            ],
            [
                'key'             => 'occasional_collection',
                'type'            => 'product',
                'title'           => 'Occasional Collection',
                'subtitle'        => 'Curated products for special occasions',
                'sort_order'      => 5,
                'items_count'     => 8,
                'desktop_columns' => 4,
                'mobile_columns'  => 2,
                'rows'            => 2,
                'extra'           => ['product_filter' => 'featured', 'category_id' => null],
            ],
            [
                'key'        => 'offer_banner',
                'type'       => 'offer_banner',
                'title'      => 'Special Offers',
                'sort_order' => 6,
                'extra'      => ['columns' => 2, 'banner_position' => 'home_offer'],
            ],
            [
                'key'             => 'customer_reviews',
                'type'            => 'reviews',
                'title'           => 'What Our Customers Say',
                'subtitle'        => 'Real reviews from real customers',
                'sort_order'      => 7,
                'items_count'     => 6,
                'display_type'    => 'carousel',
                'desktop_visible' => 3,
                'mobile_visible'  => 1,
            ],
            [
                'key'             => 'blog',
                'type'            => 'blog',
                'title'           => 'Latest from Our Blog',
                'subtitle'        => 'Tips, guides, and product updates',
                'sort_order'      => 8,
                'items_count'     => 3,
                'desktop_columns' => 3,
                'mobile_columns'  => 1,
            ],
            [
                'key'             => 'brands',
                'type'            => 'brands',
                'title'           => 'Our Brands',
                'subtitle'        => 'We carry the best brands',
                'sort_order'      => 9,
                'display_type'    => 'carousel',
                'desktop_visible' => 6,
                'mobile_visible'  => 3,
            ],
            [
                'key'        => 'faq',
                'type'       => 'faq',
                'title'      => 'Frequently Asked Questions',
                'subtitle'   => 'Find answers to the most common questions about our products and services.',
                'sort_order' => 10,
                'bg_color'   => '#f8fafc',
            ],
            [
                'key'        => 'seo_block',
                'type'       => 'seo',
                'title'      => 'About Digital Support',
                'sort_order' => 11,
                'bg_color'   => '#f9fafb',
                'extra'      => ['content' => '<p>Digital Support is your trusted partner for digital products and computer accessories. We offer a wide range of laptops, printers, accessories, and more at competitive prices with excellent customer service.</p>'],
            ],
        ];

        foreach ($sections as $data) {
            // Separate extra so we can json_encode it properly
            $extra = $data['extra'] ?? null;
            unset($data['extra']);

            $merged = array_merge($defaults, $data);
            if ($extra !== null) {
                $merged['extra'] = $extra; // model casts array -> JSON automatically
            }

            HomeSection::updateOrCreate(
                ['key' => $data['key']],
                $merged
            );
        }
    }
}
