<?php
namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Laptop & Notebook
        $laptop = Category::create(['name' => 'Laptop', 'description' => 'Laptops & Notebooks from top brands', 'is_active' => true, 'sort_order' => 1]);
        Category::create(['name' => 'Gaming Laptop', 'description' => 'High-performance gaming laptops with dedicated GPUs', 'is_active' => true, 'sort_order' => 1, 'parent_id' => $laptop->id]);
        Category::create(['name' => 'Professional Laptop', 'description' => 'Business and professional laptops', 'is_active' => true, 'sort_order' => 2, 'parent_id' => $laptop->id]);
        Category::create(['name' => 'Ultrabook', 'description' => 'Ultra-thin and lightweight laptops', 'is_active' => true, 'sort_order' => 3, 'parent_id' => $laptop->id]);
        Category::create(['name' => 'Student Laptop', 'description' => 'Affordable laptops for students', 'is_active' => true, 'sort_order' => 4, 'parent_id' => $laptop->id]);

        // Desktop PC
        $desktop = Category::create(['name' => 'Desktop PC', 'description' => 'Complete desktop PC solutions', 'is_active' => true, 'sort_order' => 2]);
        Category::create(['name' => 'Brand PC', 'description' => 'Pre-built brand desktop PCs', 'is_active' => true, 'sort_order' => 1, 'parent_id' => $desktop->id]);
        Category::create(['name' => 'Gaming PC', 'description' => 'Custom gaming desktop PCs', 'is_active' => true, 'sort_order' => 2, 'parent_id' => $desktop->id]);
        Category::create(['name' => 'All-in-One PC', 'description' => 'All-in-one desktop computers', 'is_active' => true, 'sort_order' => 3, 'parent_id' => $desktop->id]);
        Category::create(['name' => 'Mini PC', 'description' => 'Compact mini desktop PCs', 'is_active' => true, 'sort_order' => 4, 'parent_id' => $desktop->id]);

        // Desktop Components
        $components = Category::create(['name' => 'Desktop Component', 'description' => 'PC components and parts', 'is_active' => true, 'sort_order' => 3]);
        Category::create(['name' => 'Processor', 'description' => 'Intel and AMD processors', 'is_active' => true, 'sort_order' => 1, 'parent_id' => $components->id]);
        Category::create(['name' => 'Motherboard', 'description' => 'Desktop motherboards', 'is_active' => true, 'sort_order' => 2, 'parent_id' => $components->id]);
        Category::create(['name' => 'Graphics Card', 'description' => 'NVIDIA and AMD graphics cards', 'is_active' => true, 'sort_order' => 3, 'parent_id' => $components->id]);
        Category::create(['name' => 'RAM', 'description' => 'Desktop and laptop memory', 'is_active' => true, 'sort_order' => 4, 'parent_id' => $components->id]);
        Category::create(['name' => 'Power Supply', 'description' => 'PSU for desktop PCs', 'is_active' => true, 'sort_order' => 5, 'parent_id' => $components->id]);
        Category::create(['name' => 'Casing', 'description' => 'PC cases and chassis', 'is_active' => true, 'sort_order' => 6, 'parent_id' => $components->id]);
        Category::create(['name' => 'CPU Cooler', 'description' => 'Air and liquid CPU coolers', 'is_active' => true, 'sort_order' => 7, 'parent_id' => $components->id]);

        // Monitor
        $monitor = Category::create(['name' => 'Monitor', 'description' => 'Computer monitors and displays', 'is_active' => true, 'sort_order' => 4]);
        Category::create(['name' => 'Gaming Monitor', 'description' => 'High refresh rate gaming monitors', 'is_active' => true, 'sort_order' => 1, 'parent_id' => $monitor->id]);
        Category::create(['name' => 'Professional Monitor', 'description' => 'Color-accurate professional displays', 'is_active' => true, 'sort_order' => 2, 'parent_id' => $monitor->id]);
        Category::create(['name' => 'Regular Monitor', 'description' => 'Everyday use monitors', 'is_active' => true, 'sort_order' => 3, 'parent_id' => $monitor->id]);

        // Printer & Scanner
        $printer = Category::create(['name' => 'Printer', 'description' => 'Printers, scanners and office equipment', 'is_active' => true, 'sort_order' => 5]);
        Category::create(['name' => 'Ink Printer', 'description' => 'Inkjet and ink tank printers', 'is_active' => true, 'sort_order' => 1, 'parent_id' => $printer->id]);
        Category::create(['name' => 'Laser Printer', 'description' => 'Mono and color laser printers', 'is_active' => true, 'sort_order' => 2, 'parent_id' => $printer->id]);
        Category::create(['name' => 'Scanner', 'description' => 'Document and photo scanners', 'is_active' => true, 'sort_order' => 3, 'parent_id' => $printer->id]);
        Category::create(['name' => 'Printer Accessories', 'description' => 'Ink, toner and printer supplies', 'is_active' => true, 'sort_order' => 4, 'parent_id' => $printer->id]);

        // Networking
        $network = Category::create(['name' => 'Networking', 'description' => 'Routers, switches and networking gear', 'is_active' => true, 'sort_order' => 6]);
        Category::create(['name' => 'Router', 'description' => 'WiFi routers and mesh systems', 'is_active' => true, 'sort_order' => 1, 'parent_id' => $network->id]);
        Category::create(['name' => 'Switch', 'description' => 'Network switches', 'is_active' => true, 'sort_order' => 2, 'parent_id' => $network->id]);
        Category::create(['name' => 'Access Point', 'description' => 'Wireless access points', 'is_active' => true, 'sort_order' => 3, 'parent_id' => $network->id]);

        // Storage
        $storage = Category::create(['name' => 'Storage', 'description' => 'Internal and external storage solutions', 'is_active' => true, 'sort_order' => 7]);
        Category::create(['name' => 'SSD', 'description' => 'Solid state drives', 'is_active' => true, 'sort_order' => 1, 'parent_id' => $storage->id]);
        Category::create(['name' => 'Hard Disk', 'description' => 'Internal and external hard drives', 'is_active' => true, 'sort_order' => 2, 'parent_id' => $storage->id]);
        Category::create(['name' => 'Pen Drive', 'description' => 'USB flash drives', 'is_active' => true, 'sort_order' => 3, 'parent_id' => $storage->id]);

        // Accessories
        $accessories = Category::create(['name' => 'Accessories', 'description' => 'Computer peripherals and accessories', 'is_active' => true, 'sort_order' => 8]);
        Category::create(['name' => 'Keyboard', 'description' => 'Mechanical and membrane keyboards', 'is_active' => true, 'sort_order' => 1, 'parent_id' => $accessories->id]);
        Category::create(['name' => 'Mouse', 'description' => 'Wired and wireless mice', 'is_active' => true, 'sort_order' => 2, 'parent_id' => $accessories->id]);
        Category::create(['name' => 'Headphone', 'description' => 'Headphones and headsets', 'is_active' => true, 'sort_order' => 3, 'parent_id' => $accessories->id]);
        Category::create(['name' => 'Webcam', 'description' => 'USB webcams for video calls', 'is_active' => true, 'sort_order' => 4, 'parent_id' => $accessories->id]);
        Category::create(['name' => 'Speaker', 'description' => 'Desktop and portable speakers', 'is_active' => true, 'sort_order' => 5, 'parent_id' => $accessories->id]);
        Category::create(['name' => 'UPS', 'description' => 'Uninterruptible power supplies', 'is_active' => true, 'sort_order' => 6, 'parent_id' => $accessories->id]);
        Category::create(['name' => 'Cable & Adapter', 'description' => 'Cables, adapters and converters', 'is_active' => true, 'sort_order' => 7, 'parent_id' => $accessories->id]);

        // Gaming
        $gaming = Category::create(['name' => 'Gaming', 'description' => 'Gaming peripherals and accessories', 'is_active' => true, 'sort_order' => 9]);
        Category::create(['name' => 'Gaming Chair', 'description' => 'Ergonomic gaming chairs', 'is_active' => true, 'sort_order' => 1, 'parent_id' => $gaming->id]);
        Category::create(['name' => 'Gaming Keyboard', 'description' => 'Mechanical gaming keyboards', 'is_active' => true, 'sort_order' => 2, 'parent_id' => $gaming->id]);
        Category::create(['name' => 'Gaming Mouse', 'description' => 'High-DPI gaming mice', 'is_active' => true, 'sort_order' => 3, 'parent_id' => $gaming->id]);
        Category::create(['name' => 'Gaming Headset', 'description' => 'Surround sound gaming headsets', 'is_active' => true, 'sort_order' => 4, 'parent_id' => $gaming->id]);

        // Software
        Category::create(['name' => 'Software', 'description' => 'Software licenses and subscriptions', 'is_active' => true, 'sort_order' => 10]);

        // Camera
        $camera = Category::create(['name' => 'Camera', 'description' => 'Digital cameras and accessories', 'is_active' => true, 'sort_order' => 11]);
        Category::create(['name' => 'DSLR Camera', 'description' => 'DSLR and mirrorless cameras', 'is_active' => true, 'sort_order' => 1, 'parent_id' => $camera->id]);
        Category::create(['name' => 'Action Camera', 'description' => 'Action and sports cameras', 'is_active' => true, 'sort_order' => 2, 'parent_id' => $camera->id]);
        Category::create(['name' => 'Security Camera', 'description' => 'CCTV and IP security cameras', 'is_active' => true, 'sort_order' => 3, 'parent_id' => $camera->id]);
    }
}
