<?php

namespace Database\Seeders;

use App\Models\SiteContent;
use Illuminate\Database\Seeder;

class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        $contents = [

            // ─── NAVBAR ───────────────────────────────────────────────────
            ['page' => 'navbar', 'key' => 'welcome',     'label' => 'Top Bar Welcome Text',       'value_en' => 'Welcome to Digital Support',       'value_bn' => 'ডিজিটাল সাপোর্টে আপনাকে স্বাগতম', 'sort_order' => 1],
            ['page' => 'navbar', 'key' => 'contact',     'label' => 'Top Bar: Contact Link',       'value_en' => 'Contact',                          'value_bn' => 'যোগাযোগ',                          'sort_order' => 2],
            ['page' => 'navbar', 'key' => 'faq',         'label' => 'Top Bar: FAQ Link',           'value_en' => 'FAQ',                              'value_bn' => 'সাধারণ প্রশ্ন',                    'sort_order' => 3],
            ['page' => 'navbar', 'key' => 'track_order', 'label' => 'Header: Track Order Label',   'value_en' => 'Track Order',                      'value_bn' => 'অর্ডার ট্র্যাক',                  'sort_order' => 4],
            ['page' => 'navbar', 'key' => 'sign_in',     'label' => 'Header: Sign In Label',       'value_en' => 'Sign In',                          'value_bn' => 'প্রবেশ করুন',                      'sort_order' => 5],
            ['page' => 'navbar', 'key' => 'wishlist',    'label' => 'Header: Wishlist Label',       'value_en' => 'Wishlist',                         'value_bn' => 'পছন্দের তালিকা',                  'sort_order' => 6],
            ['page' => 'navbar', 'key' => 'compare',     'label' => 'Header: Compare Label',       'value_en' => 'Compare',                          'value_bn' => 'তুলনা করুন',                       'sort_order' => 7],
            ['page' => 'navbar', 'key' => 'cart',        'label' => 'Header: Cart Label',          'value_en' => 'Cart',                             'value_bn' => 'কার্ট',                            'sort_order' => 8],
            ['page' => 'navbar', 'key' => 'search_placeholder', 'label' => 'Search Box Placeholder', 'value_en' => 'Search products...', 'value_bn' => 'পণ্য খুঁজুন...', 'sort_order' => 9],

            // ─── FOOTER ───────────────────────────────────────────────────
            ['page' => 'footer', 'key' => 'tagline',                'label' => 'Company Tagline',              'value_en' => 'Your trusted partner for digital products and computer accessories. Quality products, great prices.', 'value_bn' => 'ডিজিটাল পণ্য ও কম্পিউটার আনুষাঙ্গিকের জন্য আপনার বিশ্বস্ত অংশীদার। মানসম্পন্ন পণ্য, সাশ্রয়ী মূল্য।', 'sort_order' => 1],
            ['page' => 'footer', 'key' => 'quick_links_title',      'label' => 'Quick Links Section Title',   'value_en' => 'Quick Links',                         'value_bn' => 'দ্রুত লিঙ্ক',              'sort_order' => 2],
            ['page' => 'footer', 'key' => 'categories_title',       'label' => 'Categories Section Title',    'value_en' => 'Categories',                          'value_bn' => 'বিভাগসমূহ',                'sort_order' => 3],
            ['page' => 'footer', 'key' => 'contact_title',          'label' => 'Contact Section Title',       'value_en' => 'Contact Us',                          'value_bn' => 'যোগাযোগ করুন',             'sort_order' => 4],
            ['page' => 'footer', 'key' => 'newsletter_title',       'label' => 'Newsletter Title',            'value_en' => 'Stay Updated',                        'value_bn' => 'আপডেট থাকুন',              'sort_order' => 5],
            ['page' => 'footer', 'key' => 'newsletter_subtitle',    'label' => 'Newsletter Subtitle',         'value_en' => 'Subscribe to get the latest deals & updates', 'value_bn' => 'সর্বশেষ অফার ও আপডেট পেতে সাবস্ক্রাইব করুন', 'sort_order' => 6],
            ['page' => 'footer', 'key' => 'newsletter_placeholder', 'label' => 'Newsletter Input Placeholder','value_en' => 'Your email address',                  'value_bn' => 'আপনার ইমেইল ঠিকানা',       'sort_order' => 7],
            ['page' => 'footer', 'key' => 'newsletter_btn',         'label' => 'Newsletter Button Text',      'value_en' => 'Subscribe',                           'value_bn' => 'সাবস্ক্রাইব',              'sort_order' => 8],
            ['page' => 'footer', 'key' => 'all_categories_link',    'label' => 'All Categories Link Text',    'value_en' => 'All Categories →',                    'value_bn' => 'সব বিভাগ →',               'sort_order' => 9],
            ['page' => 'footer', 'key' => 'copyright',              'label' => 'Copyright Text',              'value_en' => '© 2024 Digital Support. All Rights Reserved.', 'value_bn' => '© ২০২৪ ডিজিটাল সাপোর্ট। সর্বস্বত্ব সংরক্ষিত।', 'sort_order' => 10],

            // ─── HOME ─────────────────────────────────────────────────────
            ['page' => 'home', 'key' => 'view_all_products', 'label' => 'View All Products Button', 'value_en' => 'View All Products',                                                          'value_bn' => 'সব পণ্য দেখুন',                                              'sort_order' => 1],
            ['page' => 'home', 'key' => 'view_all',          'label' => 'Generic View All Link',    'value_en' => 'View All',                                                                   'value_bn' => 'সব দেখুন',                                                   'sort_order' => 2],
            ['page' => 'home', 'key' => 'faq_heading',       'label' => 'FAQ Section Heading',      'value_en' => 'Frequently Asked Questions',                                                  'value_bn' => 'সাধারণ জিজ্ঞাসা',                                            'sort_order' => 3],
            ['page' => 'home', 'key' => 'faq_subheading',    'label' => 'FAQ Section Subheading',   'value_en' => 'Find answers to the most common questions about our products and services.',   'value_bn' => 'আমাদের পণ্য ও সেবা সম্পর্কে সাধারণ প্রশ্নের উত্তর খুঁজুন।', 'sort_order' => 4],
            ['page' => 'home', 'key' => 'faq_cta_text',      'label' => 'FAQ CTA Text',             'value_en' => "Still have questions? We're here to help.",                                   'value_bn' => 'আরও প্রশ্ন আছে? আমরা সাহায্য করতে প্রস্তুত।',                'sort_order' => 5],
            ['page' => 'home', 'key' => 'faq_cta_btn',       'label' => 'FAQ CTA Button',           'value_en' => 'Contact Us',                                                                  'value_bn' => 'যোগাযোগ করুন',                                               'sort_order' => 6],

            // ─── PRODUCTS ─────────────────────────────────────────────────
            ['page' => 'products', 'key' => 'add_to_cart',     'label' => 'Add to Cart Button',        'value_en' => 'Add to Cart',         'value_bn' => 'কার্টে যোগ করুন',  'sort_order' => 1],
            ['page' => 'products', 'key' => 'buy_now',         'label' => 'Buy Now Button',            'value_en' => 'Buy Now',             'value_bn' => 'এখনই কিনুন',        'sort_order' => 2],
            ['page' => 'products', 'key' => 'out_of_stock',    'label' => 'Out of Stock Label',        'value_en' => 'Out of Stock',        'value_bn' => 'স্টক নেই',          'sort_order' => 3],
            ['page' => 'products', 'key' => 'in_stock',        'label' => 'In Stock Label',            'value_en' => 'In Stock',            'value_bn' => 'স্টকে আছে',         'sort_order' => 4],
            ['page' => 'products', 'key' => 'no_products',     'label' => 'No Products Found Message', 'value_en' => 'No products found',   'value_bn' => 'কোনো পণ্য পাওয়া যায়নি', 'sort_order' => 5],
            ['page' => 'products', 'key' => 'filter_title',    'label' => 'Filter Section Title',      'value_en' => 'Filter & Sort',       'value_bn' => 'ফিল্টার ও বাছাই',  'sort_order' => 6],
            ['page' => 'products', 'key' => 'sort_label',      'label' => 'Sort By Label',             'value_en' => 'Sort By',             'value_bn' => 'সাজান',             'sort_order' => 7],
            ['page' => 'products', 'key' => 'reviews_label',   'label' => 'Reviews Label',             'value_en' => 'Reviews',             'value_bn' => 'রিভিউ',             'sort_order' => 8],
            ['page' => 'products', 'key' => 'share_label',     'label' => 'Share Button Label',        'value_en' => 'Share',               'value_bn' => 'শেয়ার',            'sort_order' => 9],
            ['page' => 'products', 'key' => 'whatsapp_label',  'label' => 'WhatsApp Order Label',      'value_en' => 'Order on WhatsApp',   'value_bn' => 'হোয়াটসঅ্যাপে অর্ডার করুন', 'sort_order' => 10],
            ['page' => 'products', 'key' => 'compare_btn',     'label' => 'Compare Button',            'value_en' => 'Compare',             'value_bn' => 'তুলনা করুন',        'sort_order' => 11],
            ['page' => 'products', 'key' => 'wishlist_btn',    'label' => 'Wishlist Button',           'value_en' => 'Wishlist',            'value_bn' => 'পছন্দের তালিকা',   'sort_order' => 12],
            ['page' => 'products', 'key' => 'quick_view',      'label' => 'Quick View Button',         'value_en' => 'Quick View',          'value_bn' => 'দ্রুত দেখুন',       'sort_order' => 13],
            ['page' => 'products', 'key' => 'related_title',   'label' => 'Related Products Title',    'value_en' => 'Related Products',    'value_bn' => 'সম্পর্কিত পণ্য',   'sort_order' => 14],

            // ─── CART ─────────────────────────────────────────────────────
            ['page' => 'cart', 'key' => 'title',           'label' => 'Cart Page Title',           'value_en' => 'Shopping Cart',          'value_bn' => 'শপিং কার্ট',          'sort_order' => 1],
            ['page' => 'cart', 'key' => 'empty_title',     'label' => 'Empty Cart Title',          'value_en' => 'Your cart is empty',     'value_bn' => 'আপনার কার্ট খালি',   'sort_order' => 2],
            ['page' => 'cart', 'key' => 'empty_sub',       'label' => 'Empty Cart Subtitle',       'value_en' => 'Browse our products and add items to your cart', 'value_bn' => 'আমাদের পণ্য দেখুন এবং কার্টে যোগ করুন', 'sort_order' => 3],
            ['page' => 'cart', 'key' => 'continue',        'label' => 'Continue Shopping Button',  'value_en' => 'Continue Shopping',      'value_bn' => 'কেনাকাটা চালিয়ে যান', 'sort_order' => 4],
            ['page' => 'cart', 'key' => 'checkout_btn',    'label' => 'Checkout Button',           'value_en' => 'Proceed to Checkout',    'value_bn' => 'চেকআউটে যান',         'sort_order' => 5],
            ['page' => 'cart', 'key' => 'product_col',     'label' => 'Table: Product Column',     'value_en' => 'Product',                'value_bn' => 'পণ্য',                'sort_order' => 6],
            ['page' => 'cart', 'key' => 'price_col',       'label' => 'Table: Price Column',       'value_en' => 'Price',                  'value_bn' => 'মূল্য',               'sort_order' => 7],
            ['page' => 'cart', 'key' => 'qty_col',         'label' => 'Table: Quantity Column',    'value_en' => 'Quantity',               'value_bn' => 'পরিমাণ',              'sort_order' => 8],
            ['page' => 'cart', 'key' => 'total_col',       'label' => 'Table: Total Column',       'value_en' => 'Total',                  'value_bn' => 'মোট',                 'sort_order' => 9],
            ['page' => 'cart', 'key' => 'summary_title',   'label' => 'Order Summary Title',       'value_en' => 'Order Summary',          'value_bn' => 'অর্ডার সারসংক্ষেপ',  'sort_order' => 10],
            ['page' => 'cart', 'key' => 'subtotal',        'label' => 'Subtotal Label',            'value_en' => 'Subtotal',               'value_bn' => 'উপমোট',               'sort_order' => 11],
            ['page' => 'cart', 'key' => 'delivery',        'label' => 'Delivery Label',            'value_en' => 'Delivery',               'value_bn' => 'ডেলিভারি',            'sort_order' => 12],
            ['page' => 'cart', 'key' => 'total',           'label' => 'Total Label',               'value_en' => 'Total',                  'value_bn' => 'সর্বমোট',             'sort_order' => 13],
            ['page' => 'cart', 'key' => 'suggestions_title', 'label' => 'Suggestions Section Title', 'value_en' => 'You May Also Like', 'value_bn' => 'আপনার পছন্দ হতে পারে', 'sort_order' => 14],

            // ─── CHECKOUT ─────────────────────────────────────────────────
            ['page' => 'checkout', 'key' => 'title',             'label' => 'Page Title',                 'value_en' => 'Checkout',                       'value_bn' => 'চেকআউট',                     'sort_order' => 1],
            ['page' => 'checkout', 'key' => 'shipping_title',    'label' => 'Shipping Section Title',     'value_en' => 'Shipping Information',           'value_bn' => 'শিপিং তথ্য',                 'sort_order' => 2],
            ['page' => 'checkout', 'key' => 'billing_title',     'label' => 'Billing Section Title',     'value_en' => 'Billing Information',            'value_bn' => 'বিলিং তথ্য',                 'sort_order' => 3],
            ['page' => 'checkout', 'key' => 'same_as_shipping',  'label' => 'Same As Shipping Checkbox', 'value_en' => 'Same as shipping address',       'value_bn' => 'শিপিং ঠিকানার মতো',          'sort_order' => 4],
            ['page' => 'checkout', 'key' => 'name_label',        'label' => 'Full Name Field Label',     'value_en' => 'Full Name',                      'value_bn' => 'পুরো নাম',                   'sort_order' => 5],
            ['page' => 'checkout', 'key' => 'phone_label',       'label' => 'Phone Field Label',         'value_en' => 'Phone Number',                   'value_bn' => 'ফোন নম্বর',                  'sort_order' => 6],
            ['page' => 'checkout', 'key' => 'district_label',    'label' => 'District Field Label',      'value_en' => 'District',                       'value_bn' => 'জেলা',                        'sort_order' => 7],
            ['page' => 'checkout', 'key' => 'thana_label',       'label' => 'Thana Field Label',         'value_en' => 'Thana / Upazila',                'value_bn' => 'থানা / উপজেলা',              'sort_order' => 8],
            ['page' => 'checkout', 'key' => 'address_label',     'label' => 'Address Field Label',       'value_en' => 'Full Address',                   'value_bn' => 'পুরো ঠিকানা',                'sort_order' => 9],
            ['page' => 'checkout', 'key' => 'payment_title',     'label' => 'Payment Section Title',     'value_en' => 'Payment Method',                 'value_bn' => 'পেমেন্ট পদ্ধতি',             'sort_order' => 10],
            ['page' => 'checkout', 'key' => 'cod_label',         'label' => 'COD Option Label',          'value_en' => 'Cash on Delivery',               'value_bn' => 'ক্যাশ অন ডেলিভারি',          'sort_order' => 11],
            ['page' => 'checkout', 'key' => 'cod_desc',          'label' => 'COD Option Description',    'value_en' => 'Pay when your order is delivered','value_bn' => 'পণ্য পেলে পেমেন্ট করুন',     'sort_order' => 12],
            ['page' => 'checkout', 'key' => 'bkash_label',       'label' => 'bKash Option Label',        'value_en' => 'bKash',                          'value_bn' => 'বিকাশ',                       'sort_order' => 13],
            ['page' => 'checkout', 'key' => 'bkash_desc',        'label' => 'bKash Option Description',  'value_en' => 'Pay securely with bKash',        'value_bn' => 'বিকাশে নিরাপদে পেমেন্ট করুন', 'sort_order' => 14],
            ['page' => 'checkout', 'key' => 'online_label',      'label' => 'Online Payment Label',      'value_en' => 'Online Payment',                 'value_bn' => 'অনলাইন পেমেন্ট',             'sort_order' => 15],
            ['page' => 'checkout', 'key' => 'online_desc',       'label' => 'Online Payment Description','value_en' => 'Card, Net Banking, MFS & more via SSLCommerz', 'value_bn' => 'কার্ড, নেট ব্যাংকিং, এমএফএস ও আরও (SSLCommerz)', 'sort_order' => 16],
            ['page' => 'checkout', 'key' => 'notes_title',       'label' => 'Notes Section Title',       'value_en' => 'Special Notes',                  'value_bn' => 'বিশেষ নির্দেশনা',            'sort_order' => 17],
            ['page' => 'checkout', 'key' => 'notes_placeholder', 'label' => 'Notes Field Placeholder',   'value_en' => 'Any special instructions for your order...', 'value_bn' => 'আপনার অর্ডারের জন্য যেকোনো বিশেষ নির্দেশনা...', 'sort_order' => 18],
            ['page' => 'checkout', 'key' => 'terms',             'label' => 'Terms & Conditions Text',   'value_en' => 'I agree to the Terms & Conditions', 'value_bn' => 'আমি শর্তাবলীতে সম্মত',      'sort_order' => 19],
            ['page' => 'checkout', 'key' => 'place_order_btn',   'label' => 'Place Order Button',        'value_en' => 'Place Order',                    'value_bn' => 'অর্ডার করুন',                'sort_order' => 20],
            ['page' => 'checkout', 'key' => 'summary_title',     'label' => 'Order Summary Title',       'value_en' => 'Order Summary',                  'value_bn' => 'অর্ডার সারসংক্ষেপ',          'sort_order' => 21],
            ['page' => 'checkout', 'key' => 'coupon_title',      'label' => 'Coupon Section Title',      'value_en' => 'Coupon Code',                    'value_bn' => 'কুপন কোড',                   'sort_order' => 22],
            ['page' => 'checkout', 'key' => 'coupon_placeholder','label' => 'Coupon Field Placeholder',  'value_en' => 'Enter coupon code',              'value_bn' => 'কুপন কোড লিখুন',             'sort_order' => 23],
            ['page' => 'checkout', 'key' => 'coupon_btn',        'label' => 'Apply Coupon Button',       'value_en' => 'Apply',                          'value_bn' => 'প্রয়োগ করুন',                'sort_order' => 24],
            ['page' => 'checkout', 'key' => 'subtotal',          'label' => 'Subtotal Label',            'value_en' => 'Subtotal',                       'value_bn' => 'উপমোট',                      'sort_order' => 25],
            ['page' => 'checkout', 'key' => 'delivery',          'label' => 'Delivery Label',            'value_en' => 'Delivery',                       'value_bn' => 'ডেলিভারি',                   'sort_order' => 26],
            ['page' => 'checkout', 'key' => 'discount',          'label' => 'Discount Label',            'value_en' => 'Discount',                       'value_bn' => 'ছাড়',                         'sort_order' => 27],
            ['page' => 'checkout', 'key' => 'total',             'label' => 'Total Label',               'value_en' => 'Total',                          'value_bn' => 'সর্বমোট',                     'sort_order' => 28],
            ['page' => 'checkout', 'key' => 'name_placeholder',    'label' => 'Name Field Placeholder',    'value_en' => 'Your full name',                 'value_bn' => 'আপনার পুরো নাম',              'sort_order' => 29],
            ['page' => 'checkout', 'key' => 'phone_placeholder',   'label' => 'Phone Field Placeholder',   'value_en' => '01XXXXXXXXX',                    'value_bn' => '০১XXXXXXXXX',                 'sort_order' => 30],
            ['page' => 'checkout', 'key' => 'address_placeholder', 'label' => 'Address Field Placeholder', 'value_en' => 'House no, Road, Area...',         'value_bn' => 'বাড়ি নং, রাস্তা, এলাকা...', 'sort_order' => 31],

            // ─── CONTACT ──────────────────────────────────────────────────
            ['page' => 'contact', 'key' => 'title',               'label' => 'Page Title',                  'value_en' => 'Contact Us',                      'value_bn' => 'যোগাযোগ করুন',                  'sort_order' => 1],
            ['page' => 'contact', 'key' => 'subtitle',            'label' => 'Page Subtitle',               'value_en' => 'Get in touch with us',            'value_bn' => 'আমাদের সাথে যোগাযোগ করুন',      'sort_order' => 2],
            ['page' => 'contact', 'key' => 'name_label',          'label' => 'Name Field Label',            'value_en' => 'Full Name',                       'value_bn' => 'পুরো নাম',                      'sort_order' => 3],
            ['page' => 'contact', 'key' => 'name_placeholder',    'label' => 'Name Field Placeholder',      'value_en' => 'Your full name',                  'value_bn' => 'আপনার পুরো নাম',                'sort_order' => 4],
            ['page' => 'contact', 'key' => 'email_label',         'label' => 'Email Field Label',           'value_en' => 'Email Address',                   'value_bn' => 'ইমেইল ঠিকানা',                  'sort_order' => 5],
            ['page' => 'contact', 'key' => 'email_placeholder',   'label' => 'Email Field Placeholder',     'value_en' => 'your@email.com',                  'value_bn' => 'আপনার@ইমেইল.com',               'sort_order' => 6],
            ['page' => 'contact', 'key' => 'phone_label',         'label' => 'Phone Field Label',           'value_en' => 'Phone Number',                    'value_bn' => 'ফোন নম্বর',                     'sort_order' => 7],
            ['page' => 'contact', 'key' => 'phone_placeholder',   'label' => 'Phone Field Placeholder',     'value_en' => '01XXXXXXXXX',                     'value_bn' => '০১XXXXXXXXX',                   'sort_order' => 8],
            ['page' => 'contact', 'key' => 'subject_label',       'label' => 'Subject Field Label',         'value_en' => 'Subject',                         'value_bn' => 'বিষয়',                          'sort_order' => 9],
            ['page' => 'contact', 'key' => 'subject_placeholder', 'label' => 'Subject Field Placeholder',   'value_en' => 'How can we help?',                'value_bn' => 'কিভাবে সাহায্য করতে পারি?',     'sort_order' => 10],
            ['page' => 'contact', 'key' => 'message_label',       'label' => 'Message Field Label',         'value_en' => 'Message',                         'value_bn' => 'বার্তা',                        'sort_order' => 11],
            ['page' => 'contact', 'key' => 'message_placeholder', 'label' => 'Message Field Placeholder',   'value_en' => 'Write your message here...',      'value_bn' => 'আপনার বার্তা এখানে লিখুন...',  'sort_order' => 12],
            ['page' => 'contact', 'key' => 'submit_btn',          'label' => 'Submit Button Text',          'value_en' => 'Send Message',                    'value_bn' => 'বার্তা পাঠান',                  'sort_order' => 13],
            ['page' => 'contact', 'key' => 'success_msg',         'label' => 'Success Message',             'value_en' => 'Your message has been sent successfully!', 'value_bn' => 'আপনার বার্তা সফলভাবে পাঠানো হয়েছে!', 'sort_order' => 14],

            // ─── COMMON ───────────────────────────────────────────────────
            ['page' => 'common', 'key' => 'loading',          'label' => 'Loading Text',            'value_en' => 'Loading...',         'value_bn' => 'লোড হচ্ছে...',          'sort_order' => 1],
            ['page' => 'common', 'key' => 'view_details',     'label' => 'View Details Button',     'value_en' => 'View Details',       'value_bn' => 'বিস্তারিত দেখুন',       'sort_order' => 2],
            ['page' => 'common', 'key' => 'read_more',        'label' => 'Read More Link',          'value_en' => 'Read More',          'value_bn' => 'আরও পড়ুন',              'sort_order' => 3],
            ['page' => 'common', 'key' => 'show_less',        'label' => 'Show Less Link',          'value_en' => 'Show Less',          'value_bn' => 'কম দেখান',              'sort_order' => 4],
            ['page' => 'common', 'key' => 'search_btn',       'label' => 'Search Button',           'value_en' => 'Search',             'value_bn' => 'খুঁজুন',                'sort_order' => 5],
            ['page' => 'common', 'key' => 'no_results',       'label' => 'No Results Message',      'value_en' => 'No results found',   'value_bn' => 'কোনো ফলাফল পাওয়া যায়নি', 'sort_order' => 6],
            ['page' => 'common', 'key' => 'taka_symbol',      'label' => 'Currency Suffix',         'value_en' => '৳',                  'value_bn' => '৳',                      'sort_order' => 7],

            // ─── TRACK ORDER ──────────────────────────────────────────────
            ['page' => 'track', 'key' => 'page_title',             'label' => 'Page Title (browser tab)',      'value_en' => 'Track Your Order',                                                    'value_bn' => 'আপনার অর্ডার ট্র্যাক করুন',               'sort_order' => 1],
            ['page' => 'track', 'key' => 'breadcrumb',             'label' => 'Breadcrumb Label',              'value_en' => 'Track Order',                                                         'value_bn' => 'অর্ডার ট্র্যাক',                           'sort_order' => 2],
            ['page' => 'track', 'key' => 'heading',                'label' => 'Page Heading',                  'value_en' => 'Track Your Order',                                                    'value_bn' => 'আপনার অর্ডার ট্র্যাক করুন',               'sort_order' => 3],
            ['page' => 'track', 'key' => 'subheading',             'label' => 'Page Subheading',               'value_en' => 'Enter your order number and phone number to check your delivery status.','value_bn' => 'আপনার ডেলিভারি স্ট্যাটাস দেখতে অর্ডার নম্বর ও ফোন নম্বর দিন।', 'sort_order' => 4],
            ['page' => 'track', 'key' => 'order_number_label',     'label' => 'Order Number Field Label',      'value_en' => 'Order Number',                                                        'value_bn' => 'অর্ডার নম্বর',                             'sort_order' => 5],
            ['page' => 'track', 'key' => 'order_number_placeholder','label' => 'Order Number Placeholder',     'value_en' => 'e.g. ORD-ABC123XYZ',                                                 'value_bn' => 'যেমন ORD-ABC123XYZ',                        'sort_order' => 6],
            ['page' => 'track', 'key' => 'phone_label',            'label' => 'Phone Number Field Label',      'value_en' => 'Phone Number',                                                        'value_bn' => 'ফোন নম্বর',                                'sort_order' => 7],
            ['page' => 'track', 'key' => 'phone_placeholder',      'label' => 'Phone Number Placeholder',      'value_en' => '01XXXXXXXXX',                                                         'value_bn' => '০১XXXXXXXXX',                               'sort_order' => 8],
            ['page' => 'track', 'key' => 'track_btn',              'label' => 'Track Button Text',             'value_en' => 'Track Order',                                                         'value_bn' => 'অর্ডার ট্র্যাক করুন',                      'sort_order' => 9],
            ['page' => 'track', 'key' => 'help_text',              'label' => 'Help Text',                     'value_en' => 'Need help?',                                                          'value_bn' => 'সাহায্য দরকার?',                            'sort_order' => 10],
            ['page' => 'track', 'key' => 'contact_link',           'label' => 'Contact Us Link Text',          'value_en' => 'Contact Us',                                                          'value_bn' => 'যোগাযোগ করুন',                             'sort_order' => 11],
        ];

        foreach ($contents as $item) {
            SiteContent::updateOrCreate(
                ['page' => $item['page'], 'key' => $item['key']],
                $item
            );
        }
    }
}
