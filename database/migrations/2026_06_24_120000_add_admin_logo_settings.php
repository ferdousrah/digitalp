<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Admin-panel logos: separate light/dark variants so the brand stays
        // visible on both the light login page and the dark sidebar.
        foreach (['admin_logo_light', 'admin_logo_dark'] as $key) {
            Setting::firstOrCreate(
                ['key' => $key],
                ['group' => 'branding', 'value' => null, 'type' => 'image']
            );
        }
    }

    public function down(): void
    {
        Setting::whereIn('key', ['admin_logo_light', 'admin_logo_dark'])->delete();
    }
};
