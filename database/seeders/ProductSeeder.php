<?php
namespace Database\Seeders;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // ── Brands (matching Ryans.com vendors) ────────────────────
        $brands = [];
        $brandData = [
            ['name' => 'HP', 'description' => 'Hewlett-Packard — laptops, printers, desktops', 'website_url' => 'https://www.hp.com', 'sort_order' => 1],
            ['name' => 'Dell', 'description' => 'Dell Technologies — laptops, desktops, monitors', 'website_url' => 'https://www.dell.com', 'sort_order' => 2],
            ['name' => 'Lenovo', 'description' => 'Lenovo Group — ThinkPad, IdeaPad, Legion', 'website_url' => 'https://www.lenovo.com', 'sort_order' => 3],
            ['name' => 'ASUS', 'description' => 'ASUSTeK Computer — laptops, components, gaming', 'website_url' => 'https://www.asus.com', 'sort_order' => 4],
            ['name' => 'Acer', 'description' => 'Acer Inc. — Aspire, Nitro, Predator', 'website_url' => 'https://www.acer.com', 'sort_order' => 5],
            ['name' => 'MSI', 'description' => 'Micro-Star International — gaming laptops, components', 'website_url' => 'https://www.msi.com', 'sort_order' => 6],
            ['name' => 'Gigabyte', 'description' => 'GIGABYTE Technology — motherboards, GPUs, laptops', 'website_url' => 'https://www.gigabyte.com', 'sort_order' => 7],
            ['name' => 'Samsung', 'description' => 'Samsung Electronics — monitors, SSDs, memory', 'website_url' => 'https://www.samsung.com', 'sort_order' => 8],
            ['name' => 'Canon', 'description' => 'Canon Inc. — printers, cameras, scanners', 'website_url' => 'https://www.canon.com', 'sort_order' => 9],
            ['name' => 'Epson', 'description' => 'Seiko Epson — EcoTank printers, projectors', 'website_url' => 'https://www.epson.com', 'sort_order' => 10],
            ['name' => 'Brother', 'description' => 'Brother Industries — printers, labelers', 'website_url' => 'https://www.brother.com', 'sort_order' => 11],
            ['name' => 'Logitech', 'description' => 'Logitech International — mice, keyboards, webcams', 'website_url' => 'https://www.logitech.com', 'sort_order' => 12],
            ['name' => 'Razer', 'description' => 'Razer Inc. — gaming peripherals', 'website_url' => 'https://www.razer.com', 'sort_order' => 13],
            ['name' => 'TP-Link', 'description' => 'TP-Link Technologies — routers, networking', 'website_url' => 'https://www.tp-link.com', 'sort_order' => 14],
            ['name' => 'Intel', 'description' => 'Intel Corporation — processors, SSDs', 'website_url' => 'https://www.intel.com', 'sort_order' => 15],
            ['name' => 'AMD', 'description' => 'Advanced Micro Devices — processors, GPUs', 'website_url' => 'https://www.amd.com', 'sort_order' => 16],
            ['name' => 'BenQ', 'description' => 'BenQ Corporation — monitors, projectors', 'website_url' => 'https://www.benq.com', 'sort_order' => 17],
            ['name' => 'Corsair', 'description' => 'Corsair — memory, PSUs, gaming peripherals', 'website_url' => 'https://www.corsair.com', 'sort_order' => 18],
            ['name' => 'Western Digital', 'description' => 'WD — hard drives, SSDs, storage solutions', 'website_url' => 'https://www.westerndigital.com', 'sort_order' => 19],
            ['name' => 'Apple', 'description' => 'Apple Inc. — MacBook, iMac, accessories', 'website_url' => 'https://www.apple.com', 'sort_order' => 20],
            ['name' => 'Huawei', 'description' => 'Huawei Technologies — MateBook laptops, networking', 'website_url' => 'https://www.huawei.com', 'sort_order' => 21],
            ['name' => 'Cooler Master', 'description' => 'Cooler Master — casings, coolers, PSUs', 'website_url' => 'https://www.coolermaster.com', 'sort_order' => 22],
            ['name' => 'NZXT', 'description' => 'NZXT — PC cases, coolers, gaming accessories', 'website_url' => 'https://www.nzxt.com', 'sort_order' => 23],
            ['name' => 'Dahua', 'description' => 'Dahua Technology — security cameras, NVRs', 'website_url' => 'https://www.dahuasecurity.com', 'sort_order' => 24],
        ];

        foreach ($brandData as $bd) {
            $brands[$bd['name']] = Brand::create(array_merge($bd, ['is_active' => true]));
        }

        // ── Products (BDT pricing — realistic Bangladeshi market) ──
        $products = [
            // ═══════════════ LAPTOPS ═══════════════
            [
                'name' => 'HP Pavilion 15-eg3001TU Intel Core i5 1335U 8GB RAM 512GB SSD 15.6 Inch FHD Laptop',
                'short_description' => '13th Gen Core i5, 8GB DDR4, 512GB SSD, 15.6" FHD IPS, Windows 11',
                'description' => 'The HP Pavilion 15 delivers powerful everyday performance with the latest 13th Gen Intel Core i5 processor. Its 15.6-inch Full HD IPS display brings vivid colors to your work and entertainment. With 8GB DDR4 RAM and a 512GB NVMe SSD, multitasking and storage are handled effortlessly.',
                'price' => 68500.00, 'compare_price' => 72000.00, 'brand_id' => $brands['HP']->id,
                'is_featured' => true, 'in_stock' => true, 'stock_quantity' => 25, 'sku' => 'HP-PAV15-EG3001',
                'specifications' => ['Processor' => 'Intel Core i5-1335U (12MB Cache, up to 4.6 GHz)', 'RAM' => '8GB DDR4 3200MHz', 'Storage' => '512GB PCIe NVMe M.2 SSD', 'Display' => '15.6" FHD (1920x1080) IPS, Anti-glare', 'Graphics' => 'Intel Iris Xe', 'OS' => 'Windows 11 Home', 'Battery' => '3-cell, 41Wh', 'Weight' => '1.75 kg'],
                'categories' => ['Laptop', 'Student Laptop'],
            ],
            [
                'name' => 'Lenovo IdeaPad Slim 3 15IAH8 Intel Core i5 12450H 8GB RAM 512GB SSD 15.6 Inch FHD Laptop',
                'short_description' => '12th Gen Core i5, 8GB DDR4, 512GB SSD, 15.6" FHD, Arctic Grey',
                'description' => 'The Lenovo IdeaPad Slim 3 is a stylish and lightweight laptop powered by a 12th Gen Intel Core i5-12450H. With 8GB RAM, 512GB SSD and a vibrant 15.6" FHD display, it handles productivity and entertainment with ease.',
                'price' => 59990.00, 'compare_price' => 64000.00, 'brand_id' => $brands['Lenovo']->id,
                'is_featured' => true, 'in_stock' => true, 'stock_quantity' => 30, 'sku' => 'LEN-IPSM3-15IAH8',
                'specifications' => ['Processor' => 'Intel Core i5-12450H (12MB Cache, up to 4.4 GHz)', 'RAM' => '8GB DDR4 3200MHz', 'Storage' => '512GB PCIe Gen 4 NVMe SSD', 'Display' => '15.6" FHD (1920x1080) IPS, 300 nits', 'Graphics' => 'Intel UHD Graphics', 'OS' => 'Windows 11 Home', 'Battery' => '47Wh', 'Weight' => '1.63 kg'],
                'categories' => ['Laptop', 'Student Laptop'],
            ],
            [
                'name' => 'ASUS VivoBook 15 X1504VA Intel Core i5 1335U 8GB RAM 512GB SSD 15.6 Inch FHD Laptop',
                'short_description' => '13th Gen Core i5, 8GB, 512GB SSD, 15.6" FHD, Indie Black',
                'description' => 'The ASUS VivoBook 15 combines style with substance. Featuring an Intel Core i5-1335U processor, 8GB memory, and 512GB SSD, this laptop is perfect for students and young professionals. The NanoEdge display provides an immersive viewing experience.',
                'price' => 62500.00, 'compare_price' => 67000.00, 'brand_id' => $brands['ASUS']->id,
                'is_featured' => true, 'in_stock' => true, 'stock_quantity' => 20, 'sku' => 'ASUS-VB15-X1504VA',
                'specifications' => ['Processor' => 'Intel Core i5-1335U (12MB Cache, up to 4.6 GHz)', 'RAM' => '8GB DDR4 3200MHz (on board)', 'Storage' => '512GB M.2 NVMe PCIe 4.0 SSD', 'Display' => '15.6" FHD (1920x1080) IPS, 250 nits', 'Graphics' => 'Intel Iris Xe', 'OS' => 'Windows 11 Home', 'Battery' => '42Wh', 'Weight' => '1.7 kg'],
                'categories' => ['Laptop', 'Student Laptop'],
            ],
            [
                'name' => 'Dell Inspiron 15 3530 Intel Core i7 1355U 16GB RAM 512GB SSD 15.6 Inch FHD Laptop',
                'short_description' => '13th Gen Core i7, 16GB DDR4, 512GB SSD, 15.6" FHD, Carbon Black',
                'description' => 'The Dell Inspiron 15 3530 is a reliable workhorse. Powered by Intel Core i7-1355U with 16GB RAM and 512GB SSD, it can tackle demanding workloads while the large 15.6" FHD screen offers comfortable viewing for extended sessions.',
                'price' => 82990.00, 'compare_price' => 89000.00, 'brand_id' => $brands['Dell']->id,
                'is_featured' => true, 'in_stock' => true, 'stock_quantity' => 15, 'sku' => 'DELL-INS15-3530-I7',
                'specifications' => ['Processor' => 'Intel Core i7-1355U (12MB Cache, up to 5.0 GHz)', 'RAM' => '16GB DDR4 3200MHz', 'Storage' => '512GB PCIe NVMe SSD', 'Display' => '15.6" FHD (1920x1080) Anti-glare', 'Graphics' => 'Intel Iris Xe', 'OS' => 'Windows 11 Home', 'Battery' => '3-cell 41Wh', 'Weight' => '1.65 kg'],
                'categories' => ['Laptop', 'Professional Laptop'],
            ],
            [
                'name' => 'Acer Aspire 3 A315-44P AMD Ryzen 5 5625U 8GB RAM 512GB SSD 15.6 Inch FHD Laptop',
                'short_description' => 'Ryzen 5 5625U, 8GB DDR4, 512GB SSD, 15.6" FHD IPS, Pure Silver',
                'description' => 'The Acer Aspire 3 offers great value with the AMD Ryzen 5 5625U processor. 8GB RAM and 512GB SSD provide smooth multitasking and ample storage. The 15.6" Full HD IPS display ensures clear visuals for everyday computing.',
                'price' => 52990.00, 'compare_price' => 57000.00, 'brand_id' => $brands['Acer']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 22, 'sku' => 'ACER-A3-A315-44P',
                'specifications' => ['Processor' => 'AMD Ryzen 5 5625U (16MB Cache, up to 4.3 GHz)', 'RAM' => '8GB DDR4 3200MHz', 'Storage' => '512GB PCIe NVMe SSD', 'Display' => '15.6" FHD (1920x1080) IPS', 'Graphics' => 'AMD Radeon Graphics', 'OS' => 'Windows 11 Home', 'Battery' => '50Wh', 'Weight' => '1.78 kg'],
                'categories' => ['Laptop', 'Student Laptop'],
            ],
            [
                'name' => 'Lenovo ThinkPad E16 Gen 1 Intel Core i7 13700H 16GB RAM 512GB SSD 16 Inch WUXGA Laptop',
                'short_description' => '13th Gen Core i7 H-series, 16GB, 512GB SSD, 16" WUXGA IPS',
                'description' => 'The ThinkPad E16 delivers professional-grade performance with Intel Core i7-13700H. Its 16-inch WUXGA IPS display provides generous screen real estate. Built with legendary ThinkPad reliability, spill-resistant keyboard, and robust security features.',
                'price' => 99500.00, 'compare_price' => 105000.00, 'brand_id' => $brands['Lenovo']->id,
                'is_featured' => true, 'in_stock' => true, 'stock_quantity' => 12, 'sku' => 'LEN-TP-E16-G1-I7',
                'specifications' => ['Processor' => 'Intel Core i7-13700H (24MB Cache, up to 5.0 GHz)', 'RAM' => '16GB DDR4 3200MHz', 'Storage' => '512GB PCIe Gen 4 NVMe SSD', 'Display' => '16" WUXGA (1920x1200) IPS, 300 nits', 'Graphics' => 'Intel Iris Xe', 'OS' => 'Windows 11 Pro', 'Battery' => '57Wh', 'Weight' => '1.97 kg'],
                'categories' => ['Laptop', 'Professional Laptop'],
            ],
            [
                'name' => 'Apple MacBook Air M2 8GB RAM 256GB SSD 13.6 Inch Liquid Retina Laptop',
                'short_description' => 'Apple M2 Chip, 8-Core CPU, 8GB, 256GB SSD, 13.6" Liquid Retina',
                'description' => 'Strikingly thin, the redesigned MacBook Air is powered by the Apple M2 chip. Its 13.6-inch Liquid Retina display brings images to life with 1 billion colors, 500 nits brightness, and support for P3 wide color. Fanless design means silent operation.',
                'price' => 134900.00, 'compare_price' => 142000.00, 'brand_id' => $brands['Apple']->id,
                'is_featured' => true, 'in_stock' => true, 'stock_quantity' => 10, 'sku' => 'APL-MBA-M2-256',
                'specifications' => ['Processor' => 'Apple M2 (8-core CPU, 8-core GPU)', 'RAM' => '8GB Unified Memory', 'Storage' => '256GB SSD', 'Display' => '13.6" Liquid Retina, 2560x1664, 500 nits', 'Battery' => 'Up to 18 hours', 'Ports' => '2x Thunderbolt/USB 4, MagSafe 3, 3.5mm jack', 'Weight' => '1.24 kg'],
                'categories' => ['Laptop', 'Ultrabook'],
            ],
            [
                'name' => 'HP Victus 15 Gaming Intel Core i5 12500H RTX 3050 8GB RAM 512GB SSD 15.6 Inch FHD Laptop',
                'short_description' => 'Core i5-12500H, RTX 3050 4GB, 8GB DDR4, 512GB SSD, 144Hz',
                'description' => 'Game at peak performance with the HP Victus 15. The 12th Gen Intel Core i5-12500H processor and NVIDIA GeForce RTX 3050 deliver smooth gaming on the 144Hz FHD display. 8GB RAM and 512GB SSD ensure fast load times.',
                'price' => 89500.00, 'compare_price' => 95000.00, 'brand_id' => $brands['HP']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 14, 'sku' => 'HP-VIC15-I5-RTX3050',
                'specifications' => ['Processor' => 'Intel Core i5-12500H (18MB Cache, up to 4.5 GHz)', 'RAM' => '8GB DDR4 3200MHz', 'Storage' => '512GB PCIe NVMe SSD', 'GPU' => 'NVIDIA GeForce RTX 3050 4GB GDDR6', 'Display' => '15.6" FHD (1920x1080) IPS, 144Hz', 'OS' => 'Windows 11 Home', 'Weight' => '2.29 kg'],
                'categories' => ['Laptop', 'Gaming Laptop'],
            ],
            [
                'name' => 'ASUS ROG Strix G16 G614JU Intel Core i7 13650HX RTX 4050 16GB RAM 512GB SSD 16 Inch FHD Laptop',
                'short_description' => 'Core i7-13650HX, RTX 4050 6GB, 16GB DDR5, 512GB SSD, 165Hz',
                'description' => 'Dominate every game with the ASUS ROG Strix G16. Powered by Intel Core i7-13650HX and NVIDIA GeForce RTX 4050, this gaming powerhouse features a 165Hz FHD display for ultra-smooth visuals. 16GB DDR5 RAM and 512GB SSD ensure next-level performance.',
                'price' => 149900.00, 'compare_price' => 159000.00, 'brand_id' => $brands['ASUS']->id,
                'is_featured' => true, 'in_stock' => true, 'stock_quantity' => 8, 'sku' => 'ASUS-ROG-G614JU',
                'specifications' => ['Processor' => 'Intel Core i7-13650HX (24MB Cache, up to 4.9 GHz)', 'RAM' => '16GB DDR5 4800MHz', 'Storage' => '512GB PCIe Gen 4 NVMe SSD', 'GPU' => 'NVIDIA GeForce RTX 4050 6GB GDDR6', 'Display' => '16" FHD (1920x1080) IPS, 165Hz', 'OS' => 'Windows 11', 'Keyboard' => 'Per-Key RGB', 'Weight' => '2.5 kg'],
                'categories' => ['Laptop', 'Gaming Laptop'],
            ],
            [
                'name' => 'MSI Thin 15 B13UC Intel Core i5 13420H RTX 3050 8GB RAM 512GB SSD 15.6 Inch FHD Laptop',
                'short_description' => 'Core i5-13420H, RTX 3050 4GB, 8GB DDR4, 512GB SSD, 144Hz',
                'description' => 'The MSI Thin 15 B13UC blends performance with portability. With the Intel Core i5-13420H and NVIDIA RTX 3050, enjoy smooth gaming and creative workflows on its 144Hz display.',
                'price' => 79900.00, 'compare_price' => 85000.00, 'brand_id' => $brands['MSI']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 16, 'sku' => 'MSI-THIN15-B13UC',
                'specifications' => ['Processor' => 'Intel Core i5-13420H (12MB Cache, up to 4.6 GHz)', 'RAM' => '8GB DDR4 3200MHz', 'Storage' => '512GB NVMe SSD', 'GPU' => 'NVIDIA GeForce RTX 3050 4GB GDDR6', 'Display' => '15.6" FHD (1920x1080) IPS, 144Hz', 'OS' => 'Windows 11 Home', 'Weight' => '1.86 kg'],
                'categories' => ['Laptop', 'Gaming Laptop'],
            ],
            [
                'name' => 'Huawei MateBook D16 AMD Ryzen 5 5600H 8GB RAM 512GB SSD 16 Inch FHD Laptop',
                'short_description' => 'Ryzen 5 5600H, 8GB DDR4, 512GB SSD, 16" FHD IPS, Space Gray',
                'description' => 'The Huawei MateBook D16 features a large 16-inch FHD IPS display with a metal body that weighs just 1.7kg. Powered by AMD Ryzen 5 5600H with fast 512GB SSD storage, this is a great productivity laptop.',
                'price' => 62000.00, 'compare_price' => 67000.00, 'brand_id' => $brands['Huawei']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 18, 'sku' => 'HW-MBD16-R5',
                'specifications' => ['Processor' => 'AMD Ryzen 5 5600H (16MB Cache, up to 4.2 GHz)', 'RAM' => '8GB DDR4 3200MHz', 'Storage' => '512GB NVMe PCIe SSD', 'Display' => '16" FHD (1920x1080) IPS, 300 nits', 'Graphics' => 'AMD Radeon Graphics', 'Battery' => '60Wh', 'Weight' => '1.7 kg'],
                'categories' => ['Laptop', 'Ultrabook'],
            ],

            // ═══════════════ DESKTOP COMPONENTS ═══════════════
            [
                'name' => 'Intel Core i5-13400F 13th Gen 10-Core LGA 1700 Processor',
                'short_description' => '10 Cores (6P+4E), 16 Threads, up to 4.6 GHz, 20MB Cache',
                'description' => 'The Intel Core i5-13400F is a powerful 13th Gen desktop processor with 10 cores (6 Performance + 4 Efficient), 16 threads, and boost speeds up to 4.6 GHz. No integrated graphics — pair with a dedicated GPU for maximum performance.',
                'price' => 19500.00, 'compare_price' => 22000.00, 'brand_id' => $brands['Intel']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 35, 'sku' => 'INTEL-I5-13400F',
                'specifications' => ['Cores/Threads' => '10 Cores (6P+4E) / 16 Threads', 'Base Clock' => '2.5 GHz (P-core)', 'Boost Clock' => 'Up to 4.6 GHz', 'Cache' => '20MB Intel Smart Cache', 'TDP' => '65W', 'Socket' => 'LGA 1700', 'Memory Support' => 'DDR4-3200 / DDR5-4800'],
                'categories' => ['Desktop Component', 'Processor'],
            ],
            [
                'name' => 'AMD Ryzen 5 7600 6-Core AM5 Processor',
                'short_description' => '6 Cores, 12 Threads, up to 5.1 GHz, 32MB Cache, Wraith Stealth',
                'description' => 'The AMD Ryzen 5 7600 brings outstanding gaming and productivity performance on the AM5 platform. With 6 cores, 12 threads, and boost up to 5.1 GHz. Includes the Wraith Stealth cooler.',
                'price' => 22500.00, 'compare_price' => 25000.00, 'brand_id' => $brands['AMD']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 28, 'sku' => 'AMD-R5-7600',
                'specifications' => ['Cores/Threads' => '6 Cores / 12 Threads', 'Base Clock' => '3.8 GHz', 'Boost Clock' => 'Up to 5.1 GHz', 'Cache' => '32MB L3 + 6MB L2', 'TDP' => '65W', 'Socket' => 'AM5', 'Memory Support' => 'DDR5-5200', 'Cooler' => 'Wraith Stealth Included'],
                'categories' => ['Desktop Component', 'Processor'],
            ],
            [
                'name' => 'MSI PRO B760M-A WiFi DDR4 Intel 12th/13th Gen Micro-ATX Motherboard',
                'short_description' => 'LGA 1700, DDR4, WiFi 6E, 2.5G LAN, PCIe 4.0, M.2 Slots',
                'description' => 'The MSI PRO B760M-A WiFi DDR4 motherboard supports 12th/13th Gen Intel processors on the LGA 1700 socket. Features WiFi 6E, 2.5G LAN, PCIe 4.0, and dual M.2 slots. An excellent mid-range choice for productivity builds.',
                'price' => 14500.00, 'compare_price' => 16000.00, 'brand_id' => $brands['MSI']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 25, 'sku' => 'MSI-B760M-A-WIFI',
                'specifications' => ['Socket' => 'LGA 1700', 'Chipset' => 'Intel B760', 'Form Factor' => 'Micro-ATX', 'Memory' => '4x DDR4 DIMM, up to 128GB, 5333MHz OC', 'Storage' => '2x M.2, 4x SATA III', 'Network' => '2.5G LAN + WiFi 6E', 'Expansion' => '1x PCIe 4.0 x16'],
                'categories' => ['Desktop Component', 'Motherboard'],
            ],
            [
                'name' => 'Gigabyte B650M AORUS Elite AX AMD AM5 Micro-ATX Motherboard',
                'short_description' => 'AM5, DDR5, WiFi 6E, 2.5G LAN, PCIe 5.0, Dual M.2',
                'description' => 'The Gigabyte B650M AORUS Elite AX brings premium features to the AM5 platform. With PCIe 5.0 support, DDR5 memory, WiFi 6E, and efficient VRM design, this is ideal for AMD Ryzen 7000 series builds.',
                'price' => 18900.00, 'compare_price' => 21000.00, 'brand_id' => $brands['Gigabyte']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 20, 'sku' => 'GIG-B650M-AORUS-EL',
                'specifications' => ['Socket' => 'AM5', 'Chipset' => 'AMD B650', 'Form Factor' => 'Micro-ATX', 'Memory' => '2x DDR5 DIMM, up to 128GB, 7600MHz OC', 'Storage' => '2x M.2 (1x PCIe 5.0), 4x SATA III', 'Network' => '2.5G LAN + WiFi 6E + BT 5.2', 'Audio' => 'Realtek ALC897'],
                'categories' => ['Desktop Component', 'Motherboard'],
            ],
            [
                'name' => 'MSI GeForce RTX 4060 VENTUS 2X BLACK 8G OC Graphics Card',
                'short_description' => 'RTX 4060, 8GB GDDR6, DLSS 3, Ray Tracing, PCIe 4.0',
                'description' => 'The MSI RTX 4060 VENTUS 2X BLACK offers excellent 1080p gaming performance with NVIDIA Ada Lovelace architecture. Features DLSS 3, ray tracing, and 8GB GDDR6 memory. Dual-fan VENTUS cooling keeps temperatures low.',
                'price' => 38500.00, 'compare_price' => 42000.00, 'brand_id' => $brands['MSI']->id,
                'is_featured' => true, 'in_stock' => true, 'stock_quantity' => 12, 'sku' => 'MSI-RTX4060-V2X',
                'specifications' => ['GPU' => 'NVIDIA GeForce RTX 4060', 'Memory' => '8GB GDDR6, 128-bit', 'Boost Clock' => '2490 MHz', 'CUDA Cores' => '3072', 'Ray Tracing' => 'Yes (3rd Gen)', 'DLSS' => '3.0', 'Power' => '115W', 'Interface' => 'PCIe 4.0 x8'],
                'categories' => ['Desktop Component', 'Graphics Card'],
            ],
            [
                'name' => 'Corsair Vengeance DDR5 16GB (2x8GB) 5600MHz CL36 Desktop RAM',
                'short_description' => '16GB Kit (2x8GB), DDR5-5600, CL36, Intel XMP 3.0',
                'description' => 'Corsair Vengeance DDR5 memory delivers high-frequency DDR5 performance. With speeds up to 5600MHz and Intel XMP 3.0 support, it is optimized for the latest platforms. Compact and heat-spreader equipped.',
                'price' => 7200.00, 'compare_price' => 8500.00, 'brand_id' => $brands['Corsair']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 50, 'sku' => 'COR-V-DDR5-16G-5600',
                'specifications' => ['Capacity' => '16GB (2x8GB)', 'Type' => 'DDR5', 'Speed' => '5600MHz', 'Latency' => 'CL36-36-36-76', 'Voltage' => '1.25V', 'XMP' => 'Intel XMP 3.0'],
                'categories' => ['Desktop Component', 'RAM'],
            ],
            [
                'name' => 'Corsair RM750e 750W 80+ Gold Fully Modular ATX Power Supply',
                'short_description' => '750W, 80+ Gold, Fully Modular, Zero RPM Fan Mode, ATX 3.0',
                'description' => 'The Corsair RM750e delivers reliable, efficient power with 80+ Gold certification. Fully modular cables reduce clutter. Zero RPM fan mode provides silent operation at low loads. ATX 3.0 and PCIe 5.0 ready.',
                'price' => 10500.00, 'compare_price' => 12000.00, 'brand_id' => $brands['Corsair']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 30, 'sku' => 'COR-RM750E',
                'specifications' => ['Wattage' => '750W', 'Efficiency' => '80+ Gold', 'Modularity' => 'Fully Modular', 'Fan Size' => '120mm', 'ATX Standard' => 'ATX 3.0', 'PCIe 5.0' => '12VHPWR Connector', 'Warranty' => '7 Years'],
                'categories' => ['Desktop Component', 'Power Supply'],
            ],
            [
                'name' => 'NZXT H5 Flow Mid-Tower ATX PC Case White',
                'short_description' => 'Mid-Tower ATX, Tempered Glass, 2x 120mm Fans, USB-C',
                'description' => 'The NZXT H5 Flow offers optimal airflow with its perforated front panel. Includes two pre-installed 120mm fans, tempered glass side panel, and clean cable management. Supports up to 365mm GPUs.',
                'price' => 8900.00, 'compare_price' => 10000.00, 'brand_id' => $brands['NZXT']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 18, 'sku' => 'NZXT-H5-FLOW-W',
                'specifications' => ['Type' => 'Mid-Tower ATX', 'Motherboard Support' => 'Mini-ITX, Micro-ATX, ATX', 'Max GPU Length' => '365mm', 'Max CPU Cooler Height' => '165mm', 'Fans Included' => '2x 120mm', 'Front I/O' => '1x USB-C, 1x USB-A, Audio', 'Side Panel' => 'Tempered Glass'],
                'categories' => ['Desktop Component', 'Casing'],
            ],
            [
                'name' => 'Cooler Master Hyper 212 Halo Black CPU Air Cooler',
                'short_description' => '4 Heat Pipes, 120mm ARGB Fan, Intel/AMD Compatible',
                'description' => 'The Cooler Master Hyper 212 Halo delivers reliable CPU cooling with 4 direct-contact heat pipes and an ARGB 120mm fan. Compatible with Intel LGA 1700 and AMD AM5 sockets. Quiet and efficient for mainstream builds.',
                'price' => 4200.00, 'compare_price' => 4800.00, 'brand_id' => $brands['Cooler Master']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 40, 'sku' => 'CM-H212-HALO-BK',
                'specifications' => ['Type' => 'Tower Air Cooler', 'Heat Pipes' => '4x Direct Contact', 'Fan' => '120mm ARGB', 'Fan Speed' => '650-1800 RPM', 'TDP Rating' => '150W', 'Socket Support' => 'LGA 1700/1200/115x, AM5/AM4', 'Height' => '158mm'],
                'categories' => ['Desktop Component', 'CPU Cooler'],
            ],

            // ═══════════════ MONITORS ═══════════════
            [
                'name' => 'Samsung 24" FHD IPS 100Hz Monitor LS24C360EAWXXL',
                'short_description' => '24" FHD IPS, 100Hz, 5ms, FreeSync, HDMI + VGA',
                'description' => 'The Samsung LS24C360 delivers crisp Full HD visuals on a 24-inch IPS panel with 100Hz refresh rate. AMD FreeSync reduces screen tearing for smooth everyday computing and casual gaming.',
                'price' => 14500.00, 'compare_price' => 16000.00, 'brand_id' => $brands['Samsung']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 30, 'sku' => 'SAM-LS24C360',
                'specifications' => ['Panel' => 'IPS', 'Size' => '24 inch', 'Resolution' => '1920x1080 (FHD)', 'Refresh Rate' => '100Hz', 'Response Time' => '5ms (GtG)', 'Adaptive Sync' => 'AMD FreeSync', 'Ports' => 'HDMI, VGA', 'VESA' => '75x75mm'],
                'categories' => ['Monitor', 'Regular Monitor'],
            ],
            [
                'name' => 'BenQ MOBIUZ EX2710Q 27" 2K 165Hz IPS Gaming Monitor',
                'short_description' => '27" QHD IPS, 165Hz, 1ms MPRT, HDRi, FreeSync Premium',
                'description' => 'The BenQ EX2710Q delivers immersive gaming with a 27-inch QHD IPS panel at 165Hz. BenQ HDRi technology optimizes HDR content. Built-in speakers with treVolo audio system and FreeSync Premium ensure a premium experience.',
                'price' => 39900.00, 'compare_price' => 43000.00, 'brand_id' => $brands['BenQ']->id,
                'is_featured' => true, 'in_stock' => true, 'stock_quantity' => 10, 'sku' => 'BENQ-EX2710Q',
                'specifications' => ['Panel' => 'IPS', 'Size' => '27 inch', 'Resolution' => '2560x1440 (QHD)', 'Refresh Rate' => '165Hz', 'Response Time' => '1ms MPRT', 'HDR' => 'HDRi (VESA DisplayHDR 400)', 'Adaptive Sync' => 'AMD FreeSync Premium', 'Ports' => '2x HDMI 2.0, DP 1.4, USB-C'],
                'categories' => ['Monitor', 'Gaming Monitor'],
            ],
            [
                'name' => 'Dell UltraSharp U2723QE 27" 4K USB-C Hub Monitor',
                'short_description' => '27" 4K IPS Black, USB-C 90W, RJ45, VESA DisplayHDR 400',
                'description' => 'The Dell UltraSharp U2723QE uses IPS Black technology for deeper blacks and wider color. 4K resolution with 98% DCI-P3 coverage makes it ideal for content creation. USB-C hub delivers 90W charging and daisy-chaining.',
                'price' => 56900.00, 'compare_price' => 62000.00, 'brand_id' => $brands['Dell']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 8, 'sku' => 'DELL-U2723QE',
                'specifications' => ['Panel' => 'IPS Black', 'Size' => '27 inch', 'Resolution' => '3840x2160 (4K UHD)', 'Color' => '98% DCI-P3, Delta E<2', 'HDR' => 'VESA DisplayHDR 400', 'USB-C' => '90W Power Delivery', 'Ports' => 'HDMI, DP 1.4, USB-C, RJ45, 5x USB-A', 'VESA' => '100x100mm'],
                'categories' => ['Monitor', 'Professional Monitor'],
            ],
            [
                'name' => 'ASUS VG249Q1A 23.8" FHD 165Hz IPS Gaming Monitor',
                'short_description' => '23.8" FHD IPS, 165Hz, 1ms MPRT, FreeSync Premium, Shadow Boost',
                'description' => 'The ASUS VG249Q1A is a fast 1080p gaming monitor with a 165Hz IPS panel and 1ms MPRT response time. ASUS Extreme Low Motion Blur and FreeSync Premium deliver silky-smooth gameplay. Budget-friendly gaming display.',
                'price' => 19500.00, 'compare_price' => 22000.00, 'brand_id' => $brands['ASUS']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 22, 'sku' => 'ASUS-VG249Q1A',
                'specifications' => ['Panel' => 'IPS', 'Size' => '23.8 inch', 'Resolution' => '1920x1080 (FHD)', 'Refresh Rate' => '165Hz (OC)', 'Response Time' => '1ms MPRT', 'Adaptive Sync' => 'AMD FreeSync Premium', 'Ports' => 'HDMI 1.4 x2, DP 1.2', 'Speakers' => '2x 2W'],
                'categories' => ['Monitor', 'Gaming Monitor'],
            ],

            // ═══════════════ PRINTERS ═══════════════
            [
                'name' => 'Epson EcoTank L3250 WiFi Multifunction Ink Tank Printer',
                'short_description' => 'Print/Scan/Copy, WiFi, Ink Tank System, Borderless 4x6 Photo',
                'description' => 'The Epson EcoTank L3250 saves up to 90% on ink costs with its integrated ink tank system. WiFi connectivity, print/scan/copy, and borderless photo printing make it an ideal home and small office printer.',
                'price' => 19500.00, 'compare_price' => 22000.00, 'brand_id' => $brands['Epson']->id,
                'is_featured' => true, 'in_stock' => true, 'stock_quantity' => 35, 'sku' => 'EPS-L3250-WIFI',
                'specifications' => ['Type' => 'Ink Tank Multifunction', 'Functions' => 'Print, Scan, Copy', 'Print Speed' => '10.5 ipm (Mono), 5.0 ipm (Color)', 'Resolution' => '5760 x 1440 dpi', 'Connectivity' => 'WiFi, USB 2.0, WiFi Direct', 'Ink System' => 'EcoTank Refillable Bottles', 'Paper Size' => 'Up to A4'],
                'categories' => ['Printer', 'Ink Printer'],
            ],
            [
                'name' => 'HP LaserJet Pro MFP M428fdw Mono Laser Printer',
                'short_description' => 'Print/Scan/Copy/Fax, Duplex, WiFi, 40ppm, Laser',
                'description' => 'The HP LaserJet Pro MFP M428fdw delivers fast, professional-quality output at 40 pages per minute. Features automatic 2-sided printing, 50-sheet ADF, scan to email, and robust security. Ideal for busy offices.',
                'price' => 42000.00, 'compare_price' => 47000.00, 'brand_id' => $brands['HP']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 15, 'sku' => 'HP-LJ-M428FDW',
                'specifications' => ['Type' => 'Mono Laser MFP', 'Functions' => 'Print, Scan, Copy, Fax', 'Print Speed' => '40 ppm (Mono)', 'Resolution' => '1200 x 1200 dpi', 'Duplex' => 'Automatic', 'ADF' => '50-sheet', 'Connectivity' => 'WiFi, Ethernet, USB, Bluetooth', 'Duty Cycle' => '80,000 pages/month'],
                'categories' => ['Printer', 'Laser Printer'],
            ],
            [
                'name' => 'Canon PIXMA G3020 WiFi Multifunction Ink Tank Printer',
                'short_description' => 'Print/Scan/Copy, WiFi, Refillable Ink Tank, High Yield',
                'description' => 'The Canon PIXMA G3020 features a refillable ink tank system for ultra-low running costs. WiFi connectivity allows wireless printing from anywhere. High-volume ink bottles print up to 6,000 pages black and 7,700 pages color.',
                'price' => 17500.00, 'compare_price' => 19500.00, 'brand_id' => $brands['Canon']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 40, 'sku' => 'CANON-G3020-WIFI',
                'specifications' => ['Type' => 'Ink Tank Multifunction', 'Functions' => 'Print, Scan, Copy', 'Print Speed' => '9.1 ipm (Mono), 5.0 ipm (Color)', 'Resolution' => '4800 x 1200 dpi', 'Connectivity' => 'WiFi, USB 2.0', 'Yield' => '6,000 pages (Black), 7,700 pages (Color)', 'Paper Size' => 'Up to A4'],
                'categories' => ['Printer', 'Ink Printer'],
            ],
            [
                'name' => 'Brother DCP-T426W WiFi Multifunction Ink Tank Printer',
                'short_description' => 'Print/Scan/Copy, WiFi, Ink Tank Refill, Mobile Print',
                'description' => 'The Brother DCP-T426W offers reliable ink tank printing with WiFi connectivity. Mobile printing via Brother Mobile Connect app lets you print from smartphones. Ultra-high-yield ink bottles keep running costs low.',
                'price' => 16800.00, 'compare_price' => 18500.00, 'brand_id' => $brands['Brother']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 25, 'sku' => 'BRO-T426W',
                'specifications' => ['Type' => 'Ink Tank Multifunction', 'Functions' => 'Print, Scan, Copy', 'Print Speed' => '16 ipm (Mono), 9 ipm (Color)', 'Resolution' => '1200 x 6000 dpi', 'Connectivity' => 'WiFi, USB 2.0', 'Yield' => '7,500 pages (Black), 5,000 pages (Color)', 'Paper Tray' => '150-sheet'],
                'categories' => ['Printer', 'Ink Printer'],
            ],
            [
                'name' => 'HP LaserJet M111w Single Function Mono Laser Printer',
                'short_description' => 'Print Only, WiFi, 21ppm, Compact, 150-sheet Tray',
                'description' => 'The HP LaserJet M111w is a compact single-function laser printer. WiFi connectivity and speeds up to 21 ppm make it perfect for small offices. Easy setup via the HP Smart app.',
                'price' => 11500.00, 'compare_price' => 13000.00, 'brand_id' => $brands['HP']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 45, 'sku' => 'HP-LJ-M111W',
                'specifications' => ['Type' => 'Mono Laser', 'Functions' => 'Print Only', 'Print Speed' => '21 ppm', 'Resolution' => '600 x 600 dpi', 'Connectivity' => 'WiFi, USB 2.0', 'Paper Tray' => '150-sheet', 'Duty Cycle' => '8,000 pages/month'],
                'categories' => ['Printer', 'Laser Printer'],
            ],

            // ═══════════════ NETWORKING ═══════════════
            [
                'name' => 'TP-Link Archer AX55 AX3000 Dual Band WiFi 6 Router',
                'short_description' => 'AX3000, Dual Band, WiFi 6, Gigabit, OFDMA, 4 Antennas',
                'description' => 'The TP-Link Archer AX55 delivers WiFi 6 speeds up to 3000 Mbps. With OFDMA, MU-MIMO, and 4 external antennas, it provides efficient coverage for homes with many devices. Full Gigabit ports for wired connections.',
                'price' => 7500.00, 'compare_price' => 8500.00, 'brand_id' => $brands['TP-Link']->id,
                'is_featured' => true, 'in_stock' => true, 'stock_quantity' => 40, 'sku' => 'TPL-AX55',
                'specifications' => ['Standard' => 'WiFi 6 (802.11ax)', 'Speed' => '574 Mbps (2.4G) + 2402 Mbps (5G)', 'Antennas' => '4x External', 'Ports' => '1x Gigabit WAN + 4x Gigabit LAN + 1x USB 3.0', 'MU-MIMO' => 'Yes', 'OFDMA' => 'Yes', 'Coverage' => 'Up to 2500 sq ft'],
                'categories' => ['Networking', 'Router'],
            ],
            [
                'name' => 'TP-Link Deco M4 AC1200 Whole Home Mesh WiFi System (2-Pack)',
                'short_description' => 'AC1200, Mesh WiFi, 2-Pack, Up to 3800 sq ft, Parental Controls',
                'description' => 'The TP-Link Deco M4 mesh system blankets your entire home with seamless WiFi coverage. The 2-pack covers up to 3800 square feet, eliminating dead zones. Easy setup through the Deco app with parental controls.',
                'price' => 9800.00, 'compare_price' => 11000.00, 'brand_id' => $brands['TP-Link']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 20, 'sku' => 'TPL-DECO-M4-2PK',
                'specifications' => ['Standard' => 'WiFi 5 (802.11ac)', 'Speed' => '300 Mbps (2.4G) + 867 Mbps (5G)', 'Coverage' => 'Up to 3800 sq ft (2-pack)', 'Ports' => '2x Gigabit Ethernet per unit', 'Devices' => 'Up to 100', 'Security' => 'WPA3, TP-Link HomeCare'],
                'categories' => ['Networking', 'Router'],
            ],
            [
                'name' => 'TP-Link TL-SG108 8-Port Gigabit Desktop Switch',
                'short_description' => '8-Port 10/100/1000 Mbps, Plug & Play, Metal Case',
                'description' => 'The TP-Link TL-SG108 is a simple plug-and-play 8-port Gigabit switch. Metal housing ensures durability and heat dissipation. Green Ethernet technology saves energy by detecting link status and cable length.',
                'price' => 2200.00, 'compare_price' => 2600.00, 'brand_id' => $brands['TP-Link']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 50, 'sku' => 'TPL-SG108',
                'specifications' => ['Ports' => '8x 10/100/1000 Mbps RJ45', 'Switching Capacity' => '16 Gbps', 'MAC Table' => '4K', 'Housing' => 'Steel Case', 'Management' => 'Unmanaged', 'Power' => 'External Power Adapter'],
                'categories' => ['Networking', 'Switch'],
            ],

            // ═══════════════ STORAGE ═══════════════
            [
                'name' => 'Samsung 980 PRO 1TB PCIe Gen 4.0 NVMe M.2 SSD',
                'short_description' => '1TB, PCIe 4.0 NVMe, Up to 7000 MB/s Read, V-NAND',
                'description' => 'The Samsung 980 PRO leverages PCIe 4.0 to deliver blazing-fast read speeds up to 7,000 MB/s. Samsung V-NAND technology ensures reliability. Ideal for gamers, professionals, and PC enthusiasts seeking top-tier NVMe performance.',
                'price' => 10500.00, 'compare_price' => 12000.00, 'brand_id' => $brands['Samsung']->id,
                'is_featured' => true, 'in_stock' => true, 'stock_quantity' => 30, 'sku' => 'SAM-980PRO-1TB',
                'specifications' => ['Capacity' => '1TB', 'Interface' => 'PCIe Gen 4.0 x4, NVMe 1.3c', 'Form Factor' => 'M.2 2280', 'Sequential Read' => 'Up to 7,000 MB/s', 'Sequential Write' => 'Up to 5,100 MB/s', 'NAND' => 'Samsung V-NAND 3-bit MLC', 'TBW' => '600 TBW', 'Warranty' => '5 Years'],
                'categories' => ['Storage', 'SSD'],
            ],
            [
                'name' => 'Western Digital Blue SN580 1TB NVMe M.2 SSD',
                'short_description' => '1TB, PCIe Gen 4.0 NVMe, Up to 4150 MB/s Read',
                'description' => 'The WD Blue SN580 is a reliable NVMe SSD for everyday computing. With sequential read speeds up to 4,150 MB/s, it offers a noticeable upgrade over SATA drives. Low power consumption extends laptop battery life.',
                'price' => 7500.00, 'compare_price' => 8500.00, 'brand_id' => $brands['Western Digital']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 40, 'sku' => 'WD-SN580-1TB',
                'specifications' => ['Capacity' => '1TB', 'Interface' => 'PCIe Gen 4.0 x4, NVMe', 'Form Factor' => 'M.2 2280', 'Sequential Read' => 'Up to 4,150 MB/s', 'Sequential Write' => 'Up to 4,150 MB/s', 'TBW' => '600 TBW', 'Warranty' => '5 Years'],
                'categories' => ['Storage', 'SSD'],
            ],
            [
                'name' => 'Western Digital My Passport 2TB Portable External Hard Drive',
                'short_description' => '2TB USB 3.0, Password Protection, Auto Backup, Black',
                'description' => 'The WD My Passport 2TB portable drive keeps your files safe with hardware encryption and password protection. Auto-backup software and SuperSpeed USB 3.0 interface make it easy to carry your data everywhere.',
                'price' => 6800.00, 'compare_price' => 7500.00, 'brand_id' => $brands['Western Digital']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 35, 'sku' => 'WD-PASSPORT-2TB',
                'specifications' => ['Capacity' => '2TB', 'Interface' => 'USB 3.2 Gen 1 (USB 3.0)', 'Form Factor' => '2.5" Portable', 'Security' => '256-bit AES Hardware Encryption', 'Backup' => 'WD Backup Software', 'Compatibility' => 'Windows, Mac (reformatting required)', 'Warranty' => '3 Years'],
                'categories' => ['Storage', 'Hard Disk'],
            ],
            [
                'name' => 'Samsung BAR Plus 128GB USB 3.1 Flash Drive',
                'short_description' => '128GB, USB 3.1, 400 MB/s Read, Metal Body, Waterproof',
                'description' => 'The Samsung BAR Plus USB flash drive features a durable metal body with speeds up to 400 MB/s. Waterproof, shock-proof, magnet-proof and temperature-proof — your data stays safe. Compact and reliable.',
                'price' => 1450.00, 'compare_price' => 1700.00, 'brand_id' => $brands['Samsung']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 60, 'sku' => 'SAM-BARPLUS-128G',
                'specifications' => ['Capacity' => '128GB', 'Interface' => 'USB 3.1 Gen 1', 'Read Speed' => 'Up to 400 MB/s', 'Body' => 'Metal (Champagne Silver)', 'Durability' => 'Waterproof, Shock-proof, Magnet-proof, X-ray-proof', 'Warranty' => '5 Years'],
                'categories' => ['Storage', 'Pen Drive'],
            ],

            // ═══════════════ ACCESSORIES ═══════════════
            [
                'name' => 'Logitech MX Master 3S Wireless Performance Mouse',
                'short_description' => '8000 DPI, MagSpeed Scrolling, Quiet Clicks, USB-C, Bluetooth',
                'description' => 'The Logitech MX Master 3S is the ultimate mouse for creators and professionals. MagSpeed electromagnetic scrolling is precise enough to stop on a pixel. 8K DPI sensor tracks on any surface, including glass. Quiet clicks and ergonomic design for all-day comfort.',
                'price' => 10500.00, 'compare_price' => 12000.00, 'brand_id' => $brands['Logitech']->id,
                'is_featured' => true, 'in_stock' => true, 'stock_quantity' => 25, 'sku' => 'LOG-MXM3S',
                'specifications' => ['Sensor' => '8000 DPI Darkfield', 'Scrolling' => 'MagSpeed Electromagnetic', 'Battery' => 'Up to 70 days, USB-C charging', 'Connectivity' => 'Bluetooth LE, Logi Bolt USB', 'Buttons' => '7 Programmable', 'Multi-Device' => 'Connect up to 3 devices', 'Weight' => '141g'],
                'categories' => ['Accessories', 'Mouse'],
            ],
            [
                'name' => 'Logitech MX Keys S Advanced Wireless Illuminated Keyboard',
                'short_description' => 'Full-size, Smart Illumination, Multi-Device, USB-C, Bluetooth',
                'description' => 'The Logitech MX Keys S features perfect-stroke keys shaped for your fingertips. Smart illumination adjusts backlight to your environment. Type across up to 3 devices with seamless Easy-Switch.',
                'price' => 11500.00, 'compare_price' => 13000.00, 'brand_id' => $brands['Logitech']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 20, 'sku' => 'LOG-MXKS',
                'specifications' => ['Layout' => 'Full-size with Numpad', 'Backlight' => 'Smart Illumination', 'Battery' => '10 days (backlit), 5 months (off)', 'Connectivity' => 'Bluetooth LE, Logi Bolt USB', 'Charging' => 'USB-C', 'Multi-Device' => 'Easy-Switch (up to 3)', 'Weight' => '810g'],
                'categories' => ['Accessories', 'Keyboard'],
            ],
            [
                'name' => 'Logitech C920 HD Pro Webcam',
                'short_description' => '1080p Full HD, 30fps, Stereo Mic, Autofocus, USB-A',
                'description' => 'The Logitech C920 HD Pro delivers sharp 1080p video at 30fps with automatic HD light correction. Dual stereo microphones capture clear audio. Wide 78-degree field of view with autofocus ensures you always look your best.',
                'price' => 7200.00, 'compare_price' => 8000.00, 'brand_id' => $brands['Logitech']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 30, 'sku' => 'LOG-C920-PRO',
                'specifications' => ['Resolution' => '1080p Full HD @ 30fps', 'Field of View' => '78 degrees', 'Focus' => 'Autofocus', 'Microphone' => 'Dual Stereo Mics', 'Connection' => 'USB-A', 'Mount' => 'Universal clip (monitors + tripods)', 'Privacy' => 'N/A (no shutter)'],
                'categories' => ['Accessories', 'Webcam'],
            ],
            [
                'name' => 'Edifier R1280T 2.0 Active Bookshelf Studio Monitor Speaker',
                'short_description' => '42W RMS, MDF Wood, Dual RCA Inputs, Remote Control',
                'description' => 'The Edifier R1280T delivers warm, balanced audio from 4-inch bass drivers and calibrated flared bass reflex ports. Dual RCA inputs let you connect two audio sources simultaneously. Includes wireless remote for volume/mute.',
                'price' => 5500.00, 'compare_price' => 6200.00, 'brand_id' => $brands['Logitech']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 18, 'sku' => 'EDIFIER-R1280T',
                'specifications' => ['Type' => '2.0 Active Bookshelf', 'Total Power' => '42W RMS (21W x 2)', 'Drivers' => '4" Bass + 13mm Tweeter', 'Input' => 'Dual RCA', 'Material' => 'MDF Wood Enclosure', 'Remote' => 'Wireless', 'Frequency Response' => '75Hz - 18KHz'],
                'categories' => ['Accessories', 'Speaker'],
            ],

            // ═══════════════ GAMING ═══════════════
            [
                'name' => 'Razer BlackWidow V4 75% Mechanical Gaming Keyboard',
                'short_description' => '75% Layout, Hot-Swappable, Razer Orange Switches, RGB, Knob',
                'description' => 'The Razer BlackWidow V4 75% offers a compact layout with hot-swappable mechanical switches. Razer Orange Switches provide tactile, silent actuation. Customizable knob and per-key RGB Chroma lighting complete the package.',
                'price' => 15500.00, 'compare_price' => 17000.00, 'brand_id' => $brands['Razer']->id,
                'is_featured' => true, 'in_stock' => true, 'stock_quantity' => 15, 'sku' => 'RZR-BW-V4-75',
                'specifications' => ['Layout' => '75% Compact', 'Switches' => 'Razer Orange Mechanical (Tactile)', 'Hot-Swappable' => 'Yes', 'Lighting' => 'Per-Key Razer Chroma RGB', 'Media Control' => 'Rotary Knob', 'Connection' => 'USB-C Wired', 'Keycaps' => 'Doubleshot PBT'],
                'categories' => ['Gaming', 'Gaming Keyboard'],
            ],
            [
                'name' => 'Razer DeathAdder V3 HyperSpeed Wireless Gaming Mouse',
                'short_description' => '30000 DPI, 90hr Battery, 63g Ultra-Light, HyperSpeed Wireless',
                'description' => 'The Razer DeathAdder V3 HyperSpeed combines legendary ergonomics with cutting-edge performance. Focus Pro 30K sensor, HyperSpeed wireless technology, and ultra-light 63g design deliver an uncompromising competitive advantage.',
                'price' => 9500.00, 'compare_price' => 11000.00, 'brand_id' => $brands['Razer']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 20, 'sku' => 'RZR-DA-V3-HS',
                'specifications' => ['Sensor' => 'Razer Focus Pro 30K DPI', 'Battery' => 'Up to 90 hours (HyperSpeed)', 'Weight' => '63g', 'Connectivity' => 'HyperSpeed Wireless, Bluetooth, USB-C', 'Switches' => 'Razer Gen-3 Mechanical', 'Polling Rate' => '1000 Hz (4000 Hz with dongle)', 'Buttons' => '5'],
                'categories' => ['Gaming', 'Gaming Mouse'],
            ],
            [
                'name' => 'Razer BlackShark V2 X USB Wired Gaming Headset',
                'short_description' => '7.1 Surround, 50mm TriForce Drivers, Cardioid Mic, USB',
                'description' => 'The Razer BlackShark V2 X USB delivers immersive 7.1 surround sound with custom-tuned 50mm TriForce Titanium drivers. The advanced passive noise isolation and bendable cardioid microphone are perfect for competitive gaming.',
                'price' => 5200.00, 'compare_price' => 6000.00, 'brand_id' => $brands['Razer']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 25, 'sku' => 'RZR-BSV2X-USB',
                'specifications' => ['Driver' => '50mm TriForce Titanium', 'Surround' => '7.1 (USB)', 'Microphone' => 'Cardioid (Bendable)', 'Connection' => 'USB-A', 'Frequency Response' => '12Hz - 28KHz', 'Weight' => '240g', 'Earcup' => 'Memory Foam with FlowKnit'],
                'categories' => ['Gaming', 'Gaming Headset'],
            ],

            // ═══════════════ DESKTOP PCs ═══════════════
            [
                'name' => 'HP ProDesk 400 G9 SFF Intel Core i5 12500 8GB RAM 512GB SSD Desktop PC',
                'short_description' => 'Core i5-12500, 8GB DDR4, 512GB SSD, Win 11 Pro, Small Form Factor',
                'description' => 'The HP ProDesk 400 G9 SFF is a business-class desktop that delivers high performance in a compact form. Intel Core i5-12500, 8GB RAM, 512GB SSD, and Windows 11 Pro make it ideal for modern offices.',
                'price' => 68000.00, 'compare_price' => 73000.00, 'brand_id' => $brands['HP']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 10, 'sku' => 'HP-PD400-G9-SFF',
                'specifications' => ['Processor' => 'Intel Core i5-12500 (18MB, up to 4.6 GHz)', 'RAM' => '8GB DDR4 3200MHz', 'Storage' => '512GB PCIe NVMe SSD', 'Graphics' => 'Intel UHD Graphics 770', 'OS' => 'Windows 11 Pro', 'Form Factor' => 'Small Form Factor', 'Ports' => '4x USB-A, 2x USB-C, VGA, DP, RJ45'],
                'categories' => ['Desktop PC', 'Brand PC'],
            ],
            [
                'name' => 'Lenovo IdeaCentre AIO 3 27IAP7 Core i5 12450H 8GB RAM 512GB SSD 27 Inch FHD All-in-One PC',
                'short_description' => 'Core i5-12450H, 8GB, 512GB SSD, 27" FHD IPS, Webcam, WiFi 6',
                'description' => 'The Lenovo IdeaCentre AIO 3 27 packs a full desktop experience into a sleek all-in-one design. 27-inch FHD IPS display, 12th Gen Core i5, 8GB RAM, 512GB SSD, built-in webcam and speakers. Clutter-free computing.',
                'price' => 75000.00, 'compare_price' => 80000.00, 'brand_id' => $brands['Lenovo']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 8, 'sku' => 'LEN-AIO3-27IAP7',
                'specifications' => ['Processor' => 'Intel Core i5-12450H', 'RAM' => '8GB DDR4 3200MHz', 'Storage' => '512GB PCIe Gen 4 SSD', 'Display' => '27" FHD (1920x1080) IPS', 'Graphics' => 'Intel UHD Graphics', 'OS' => 'Windows 11 Home', 'Webcam' => 'FHD with Privacy Shutter', 'WiFi' => 'WiFi 6'],
                'categories' => ['Desktop PC', 'All-in-One PC'],
            ],

            // ═══════════════ CAMERAS ═══════════════
            [
                'name' => 'Canon EOS R50 Mirrorless Camera with RF-S 18-45mm f/4.5-6.3 IS STM Lens',
                'short_description' => '24.2MP APS-C, 4K Video, Subject Detection AF, WiFi/BT',
                'description' => 'The Canon EOS R50 is a compact and lightweight mirrorless camera with a 24.2MP APS-C sensor. Advanced subject detection AF tracks people, animals, and vehicles. 4K video recording and WiFi/Bluetooth connectivity for instant sharing.',
                'price' => 82000.00, 'compare_price' => 88000.00, 'brand_id' => $brands['Canon']->id,
                'is_featured' => true, 'in_stock' => true, 'stock_quantity' => 6, 'sku' => 'CANON-EOSR50-KIT',
                'specifications' => ['Sensor' => '24.2MP APS-C CMOS', 'Mount' => 'Canon RF', 'AF System' => 'Dual Pixel CMOS AF II, Subject Detection', 'Video' => '4K 30p / 1080p 120p', 'ISO Range' => '100-32000 (expandable to 51200)', 'Lens' => 'RF-S 18-45mm f/4.5-6.3 IS STM', 'Connectivity' => 'WiFi, Bluetooth 4.2', 'Weight' => '375g (body only)'],
                'categories' => ['Camera', 'DSLR Camera'],
            ],
            [
                'name' => 'Dahua IPC-HFW1230S1P 2MP IR Bullet IP Camera',
                'short_description' => '2MP, 30m IR, IP67, H.265+, PoE, 2.8mm Fixed Lens',
                'description' => 'The Dahua IPC-HFW1230S1P is a reliable 2MP bullet IP camera with 30-meter infrared range and IP67 weatherproof rating. H.265+ compression saves storage. PoE support simplifies installation with a single cable.',
                'price' => 4200.00, 'compare_price' => 4800.00, 'brand_id' => $brands['Dahua']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 50, 'sku' => 'DAHUA-HFW1230S1P',
                'specifications' => ['Resolution' => '2MP (1920x1080)', 'Lens' => '2.8mm Fixed', 'IR Range' => 'Up to 30m', 'Compression' => 'H.265+ / H.264+', 'Weather Rating' => 'IP67', 'Power' => 'PoE (802.3af) / DC 12V', 'Storage' => 'MicroSD up to 256GB'],
                'categories' => ['Camera', 'Security Camera'],
            ],

            // ═══════════════ UPS ═══════════════
            [
                'name' => 'APC Back-UPS BX1100C-IN 1100VA 660W Line Interactive UPS',
                'short_description' => '1100VA/660W, 4+2 Outlets, AVR, USB, Battery Backup',
                'description' => 'The APC Back-UPS BX1100C protects your home electronics and computers from power outages and surges. 1100VA/660W capacity with AVR (Automatic Voltage Regulation) ensures clean and stable power. USB port for monitoring.',
                'price' => 7500.00, 'compare_price' => 8500.00, 'brand_id' => $brands['Logitech']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 20, 'sku' => 'APC-BX1100C-IN',
                'specifications' => ['Capacity' => '1100VA / 660W', 'Topology' => 'Line Interactive', 'Outlets' => '4x Battery Backup + 2x Surge Only', 'AVR' => 'Automatic Voltage Regulation', 'Transfer Time' => '6-10ms', 'USB Port' => 'Yes', 'Form Factor' => 'Tower'],
                'categories' => ['Accessories', 'UPS'],
            ],

            // ═══════════════ MORE LAPTOPS (additional variety) ═══════════════
            [
                'name' => 'Lenovo LOQ 15IAX9 Intel Core i5 12450HX RTX 4050 16GB RAM 512GB SSD 15.6 Inch FHD Gaming Laptop',
                'short_description' => 'Core i5-12450HX, RTX 4050 6GB, 16GB DDR5, 512GB SSD, 144Hz',
                'description' => 'The Lenovo LOQ 15IAX9 is a budget gaming powerhouse with Intel Core i5-12450HX and NVIDIA RTX 4050 6GB. 16GB DDR5 RAM and 144Hz FHD display ensure fluid gameplay. Nahimic 3D audio for immersive gaming.',
                'price' => 97500.00, 'compare_price' => 105000.00, 'brand_id' => $brands['Lenovo']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 12, 'sku' => 'LEN-LOQ-15IAX9',
                'specifications' => ['Processor' => 'Intel Core i5-12450HX (12MB Cache, up to 4.4 GHz)', 'RAM' => '16GB DDR5 4800MHz', 'Storage' => '512GB PCIe Gen 4 NVMe SSD', 'GPU' => 'NVIDIA GeForce RTX 4050 6GB GDDR6', 'Display' => '15.6" FHD (1920x1080) IPS, 144Hz', 'OS' => 'Windows 11 Home', 'Battery' => '60Wh', 'Weight' => '2.38 kg'],
                'categories' => ['Laptop', 'Gaming Laptop'],
            ],
            [
                'name' => 'Dell Latitude 5540 Intel Core i5 1345U 16GB RAM 512GB SSD 15.6 Inch FHD Business Laptop',
                'short_description' => '13th Gen vPro i5, 16GB, 512GB SSD, 15.6" FHD, Thunderbolt 4',
                'description' => 'The Dell Latitude 5540 is built for enterprise. Intel Core i5-1345U with vPro, 16GB RAM, 512GB SSD. Thunderbolt 4, IR camera for Windows Hello, and Dell Optimizer for intelligent tuning. MIL-STD-810H tested.',
                'price' => 112000.00, 'compare_price' => 120000.00, 'brand_id' => $brands['Dell']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 8, 'sku' => 'DELL-LAT5540-I5',
                'specifications' => ['Processor' => 'Intel Core i5-1345U vPro (12MB, up to 4.7 GHz)', 'RAM' => '16GB DDR4 3200MHz', 'Storage' => '512GB PCIe NVMe SSD', 'Display' => '15.6" FHD (1920x1080) Anti-glare', 'Graphics' => 'Intel Iris Xe', 'OS' => 'Windows 11 Pro', 'Ports' => '2x Thunderbolt 4, HDMI 2.0, USB-A 3.2', 'Weight' => '1.66 kg'],
                'categories' => ['Laptop', 'Professional Laptop'],
            ],

            // ═══════════════ MORE COMPONENTS ═══════════════
            [
                'name' => 'ASUS DUAL GeForce RTX 4070 SUPER OC Edition 12GB Graphics Card',
                'short_description' => 'RTX 4070 SUPER, 12GB GDDR6X, DLSS 3.5, Ray Tracing, Dual Fans',
                'description' => 'The ASUS DUAL RTX 4070 SUPER delivers exceptional 1440p gaming with 12GB GDDR6X memory. Ada Lovelace architecture with DLSS 3.5 and advanced ray tracing. ASUS Dual fan design ensures cool and quiet operation.',
                'price' => 72500.00, 'compare_price' => 78000.00, 'brand_id' => $brands['ASUS']->id,
                'is_featured' => true, 'in_stock' => true, 'stock_quantity' => 6, 'sku' => 'ASUS-RTX4070S-DUAL',
                'specifications' => ['GPU' => 'NVIDIA GeForce RTX 4070 SUPER', 'Memory' => '12GB GDDR6X, 192-bit', 'Boost Clock' => '2565 MHz (OC)', 'CUDA Cores' => '7168', 'Ray Tracing' => 'Yes (3rd Gen)', 'DLSS' => '3.5', 'Power' => '220W', 'Interface' => 'PCIe 4.0 x16'],
                'categories' => ['Desktop Component', 'Graphics Card'],
            ],
            [
                'name' => 'Corsair Vengeance DDR4 16GB (2x8GB) 3200MHz CL16 Desktop RAM',
                'short_description' => '16GB Kit (2x8GB), DDR4-3200, CL16, Intel XMP 2.0',
                'description' => 'Corsair Vengeance DDR4 is optimized for high-performance overclocking. Tuned for Intel and AMD platforms, it supports Intel XMP 2.0 for simple one-setting setup. Tight CL16 latency for responsive computing.',
                'price' => 4800.00, 'compare_price' => 5500.00, 'brand_id' => $brands['Corsair']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 55, 'sku' => 'COR-V-DDR4-16G-3200',
                'specifications' => ['Capacity' => '16GB (2x8GB)', 'Type' => 'DDR4', 'Speed' => '3200MHz', 'Latency' => 'CL16-20-20-38', 'Voltage' => '1.35V', 'XMP' => 'Intel XMP 2.0'],
                'categories' => ['Desktop Component', 'RAM'],
            ],

            // ═══════════════ MORE MONITORS ═══════════════
            [
                'name' => 'HP M27fw 27" FHD IPS Monitor',
                'short_description' => '27" FHD IPS, 75Hz, 5ms, AMD FreeSync, Eye Ease, USB-C',
                'description' => 'The HP M27fw delivers crisp visuals on a 27-inch Full HD IPS display with ultra-thin bezels. AMD FreeSync reduces tearing, and HP Eye Ease with Eyesafe certification minimizes blue light without distorting color.',
                'price' => 22500.00, 'compare_price' => 25000.00, 'brand_id' => $brands['HP']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 18, 'sku' => 'HP-M27FW',
                'specifications' => ['Panel' => 'IPS', 'Size' => '27 inch', 'Resolution' => '1920x1080 (FHD)', 'Refresh Rate' => '75Hz', 'Response Time' => '5ms (GtG)', 'Adaptive Sync' => 'AMD FreeSync', 'Ports' => 'HDMI 1.4, VGA', 'Eye Care' => 'HP Eye Ease (Eyesafe certified)'],
                'categories' => ['Monitor', 'Regular Monitor'],
            ],

            // ═══════════════ ADDITIONAL PRINTERS ═══════════════
            [
                'name' => 'Epson L805 6-Color Wi-Fi Ink Tank Photo Printer',
                'short_description' => '6-Color Ink Tank, WiFi, Photo Printing, CD/DVD Print, Borderless',
                'description' => 'The Epson L805 is a high-quality photo printer with 6-color ink tank system. Print borderless photos up to A4 and even directly on CDs/DVDs. WiFi connectivity and low ink costs make it ideal for photo enthusiasts.',
                'price' => 24500.00, 'compare_price' => 27000.00, 'brand_id' => $brands['Epson']->id,
                'is_featured' => false, 'in_stock' => true, 'stock_quantity' => 12, 'sku' => 'EPS-L805',
                'specifications' => ['Type' => '6-Color Ink Tank Photo Printer', 'Functions' => 'Print Only', 'Print Speed' => '37 sec (4R borderless photo)', 'Resolution' => '5760 x 1440 dpi', 'Connectivity' => 'WiFi, USB 2.0', 'Special' => 'CD/DVD Printing, Borderless A4', 'Ink Colors' => '6 (CMYK + Light Cyan + Light Magenta)'],
                'categories' => ['Printer', 'Ink Printer'],
            ],
        ];

        foreach ($products as $productData) {
            $categoryNames = $productData['categories'];
            unset($productData['categories']);

            $product = Product::create($productData);

            $categoryIds = Category::whereIn('name', $categoryNames)->pluck('id');
            $product->categories()->attach($categoryIds);
        }
    }
}
