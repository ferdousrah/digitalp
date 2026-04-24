<?php
namespace Database\Seeders;

use App\Models\Page;
use App\Models\Service;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\Testimonial;
use App\Models\CompanyTimeline;
use App\Models\TeamMember;
use App\Models\Slider;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        // Pages
        Page::create(['title' => 'About Us', 'slug' => 'about', 'template' => 'about', 'content' => '<p>Digital Support is your trusted partner for digital products and computer accessories. Founded with the vision of making technology accessible to everyone, we offer a curated selection of laptops, printers, accessories, and more from the world\'s leading brands.</p><p>Our team of technology experts is dedicated to helping you find the right products for your needs, whether you\'re a business professional, creative artist, or everyday user.</p>', 'is_active' => true, 'meta_title' => 'About Digital Support - Your Digital Products Partner', 'meta_description' => 'Learn about Digital Support, your trusted source for computer accessories, laptops, printers, and digital products.']);
        Page::create(['title' => 'Privacy Policy', 'slug' => 'privacy-policy', 'template' => 'default', 'content' => '<h2>Privacy Policy</h2><p>Your privacy is important to us. This privacy policy explains how we collect, use, and protect your personal information.</p>', 'is_active' => true]);
        Page::create(['title' => 'Terms of Service', 'slug' => 'terms-of-service', 'template' => 'default', 'content' => '<h2>Terms of Service</h2><p>By using our website, you agree to these terms of service.</p>', 'is_active' => true]);

        // Services
        $services = [
            ['title' => 'Product Sales', 'short_description' => 'Wide range of digital products from top brands', 'description' => 'We offer an extensive catalog of laptops, printers, computer accessories, networking equipment, and software from industry-leading brands.', 'icon' => 'shopping-cart', 'is_active' => true, 'is_featured' => true, 'sort_order' => 1],
            ['title' => 'Technical Support', 'short_description' => '24/7 expert technical assistance', 'description' => 'Our certified technicians provide round-the-clock support for all your digital product needs, from setup to troubleshooting.', 'icon' => 'wrench', 'is_active' => true, 'is_featured' => true, 'sort_order' => 2],
            ['title' => 'Business Solutions', 'short_description' => 'Customized IT solutions for businesses', 'description' => 'We help businesses of all sizes with IT procurement, setup, and ongoing management of their technology infrastructure.', 'icon' => 'briefcase', 'is_active' => true, 'is_featured' => true, 'sort_order' => 3],
            ['title' => 'Warranty & Repairs', 'short_description' => 'Comprehensive warranty and repair services', 'description' => 'Extended warranty options and professional repair services to keep your devices running at peak performance.', 'icon' => 'shield-check', 'is_active' => true, 'is_featured' => false, 'sort_order' => 4],
        ];
        foreach ($services as $service) { Service::create($service); }

        // FAQ Categories and FAQs
        $generalFaq = FaqCategory::create(['name' => 'General', 'sort_order' => 1]);
        $ordersFaq = FaqCategory::create(['name' => 'Orders & Shipping', 'sort_order' => 2]);
        $returnsFaq = FaqCategory::create(['name' => 'Returns & Warranty', 'sort_order' => 3]);

        $faqs = [
            ['faq_category_id' => $generalFaq->id, 'question' => 'What products does Digital Support sell?', 'answer' => 'We sell a wide range of digital products including laptops, printers, computer accessories, monitors, storage devices, networking equipment, and software.', 'sort_order' => 1],
            ['faq_category_id' => $generalFaq->id, 'question' => 'Do you offer bulk/corporate pricing?', 'answer' => 'Yes! We offer special pricing for bulk orders and corporate clients. Contact our sales team for a custom quote.', 'sort_order' => 2],
            ['faq_category_id' => $ordersFaq->id, 'question' => 'How long does shipping take?', 'answer' => 'Standard shipping takes 3-5 business days. Express shipping (1-2 business days) is available at checkout.', 'sort_order' => 1],
            ['faq_category_id' => $ordersFaq->id, 'question' => 'Do you ship internationally?', 'answer' => 'Currently we ship within the country. International shipping is coming soon.', 'sort_order' => 2],
            ['faq_category_id' => $returnsFaq->id, 'question' => 'What is your return policy?', 'answer' => 'We accept returns within 30 days of purchase for unopened items in original packaging. Defective items can be returned within 90 days.', 'sort_order' => 1],
            ['faq_category_id' => $returnsFaq->id, 'question' => 'How do I claim warranty?', 'answer' => 'Contact our support team with your order number and product details. We will guide you through the warranty claim process.', 'sort_order' => 2],
        ];
        foreach ($faqs as $faq) { Faq::create(array_merge($faq, ['is_active' => true])); }

        // Testimonials
        $testimonials = [
            ['client_name' => 'John Smith', 'client_position' => 'IT Manager', 'client_company' => 'Tech Corp', 'content' => 'Digital Support has been our go-to supplier for all office technology. Their product range and customer service are exceptional.', 'rating' => 5, 'sort_order' => 1],
            ['client_name' => 'Sarah Johnson', 'client_position' => 'Freelance Designer', 'client_company' => null, 'content' => 'I purchased my design workstation and peripherals from Digital Support. The advice from their team helped me choose the perfect setup.', 'rating' => 5, 'sort_order' => 2],
            ['client_name' => 'Mike Chen', 'client_position' => 'CEO', 'client_company' => 'StartupHub', 'content' => 'We outfitted our entire startup office through Digital Support. Great bulk pricing and fast delivery. Highly recommended!', 'rating' => 4, 'sort_order' => 3],
        ];
        foreach ($testimonials as $t) { Testimonial::create(array_merge($t, ['is_active' => true])); }

        // Company Timeline
        $timeline = [
            ['title' => 'Company Founded', 'description' => 'Digital Support was established with a mission to make quality technology accessible.', 'year' => '2018', 'icon' => 'flag', 'sort_order' => 1],
            ['title' => 'Expanded Product Line', 'description' => 'Added printers, networking equipment, and software to our catalog.', 'year' => '2019', 'icon' => 'plus-circle', 'sort_order' => 2],
            ['title' => 'Corporate Solutions Launch', 'description' => 'Launched our business solutions division for corporate clients.', 'year' => '2020', 'icon' => 'briefcase', 'sort_order' => 3],
            ['title' => 'Online Store Launch', 'description' => 'Launched our full-featured online store with nationwide delivery.', 'year' => '2023', 'icon' => 'globe', 'sort_order' => 4],
            ['title' => 'Going Digital', 'description' => 'Launched the new Digital Support website with enhanced features.', 'year' => '2026', 'icon' => 'rocket', 'sort_order' => 5],
        ];
        foreach ($timeline as $t) { CompanyTimeline::create(array_merge($t, ['is_active' => true])); }

        // Team Members
        $team = [
            ['name' => 'Alex Thompson', 'position' => 'CEO & Founder', 'bio' => 'Alex founded Digital Support with a passion for making technology accessible. With over 15 years of experience in the tech industry.', 'sort_order' => 1],
            ['name' => 'Maria Garcia', 'position' => 'Head of Sales', 'bio' => 'Maria leads our sales team with enthusiasm and expertise. She ensures every customer finds the perfect product for their needs.', 'sort_order' => 2],
            ['name' => 'David Park', 'position' => 'Technical Director', 'bio' => 'David oversees all technical operations and ensures our product lineup meets the highest quality standards.', 'sort_order' => 3],
            ['name' => 'Emma Wilson', 'position' => 'Customer Support Lead', 'bio' => 'Emma and her team provide outstanding customer support, ensuring every client has a great experience.', 'sort_order' => 4],
        ];
        foreach ($team as $t) { TeamMember::create(array_merge($t, ['is_active' => true])); }

        // Sliders
        Slider::create(['title' => 'Welcome to Digital Support', 'subtitle' => 'Your one-stop shop for laptops, printers, and computer accessories from top brands.', 'button_text' => 'Shop Now', 'button_url' => '/products', 'position' => 'home_hero', 'is_active' => true, 'sort_order' => 1]);
        Slider::create(['title' => 'Premium Laptops', 'subtitle' => 'Discover our collection of high-performance laptops from HP, Dell, Lenovo, and ASUS.', 'button_text' => 'Browse Laptops', 'button_url' => '/categories/laptops', 'position' => 'home_hero', 'is_active' => true, 'sort_order' => 2]);
        Slider::create(['title' => 'Business Solutions', 'subtitle' => 'Custom IT solutions for businesses of all sizes. Get a free consultation today.', 'button_text' => 'Contact Us', 'button_url' => '/contact', 'position' => 'home_hero', 'is_active' => true, 'sort_order' => 3]);
    }
}
