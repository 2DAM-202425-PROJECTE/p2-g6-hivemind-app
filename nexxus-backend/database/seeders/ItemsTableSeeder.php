<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('items')->truncate();

        $items = [
            // Subscriptions (unchanged)
            ['id' => 1, 'name' => 'Basic', 'price' => 0, 'iconUrl' => 'https://api.iconify.design/lucide/sprout.svg', 'type' => 'subscription'],
            ['id' => 2, 'name' => 'Premium', 'price' => 9.99, 'iconUrl' => 'https://api.iconify.design/lucide/shield-check.svg', 'type' => 'subscription'],
            ['id' => 3, 'name' => 'Elite', 'price' => 19.99, 'iconUrl' => 'https://api.iconify.design/lucide/crown.svg', 'type' => 'subscription'],

            // Credit Packs (unchanged)
            ['id' => 4, 'name' => '100 Credits', 'price' => 1.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack'],
            ['id' => 5, 'name' => '500 Credits', 'price' => 7.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack'],
            ['id' => 6, 'name' => '1000 Credits', 'price' => 14.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack'],
            ['id' => 7, 'name' => '2500 Credits', 'price' => 34.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack'],

            // Profile Icons (unchanged)
            ['id' => 8, 'name' => 'Mini Crown', 'price' => 150, 'iconUrl' => 'https://api.iconify.design/lucide/crown.svg', 'type' => 'profile_icon'],
            ['id' => 9, 'name' => 'Shining Star', 'price' => 100, 'iconUrl' => 'https://api.iconify.design/lucide/star.svg', 'type' => 'profile_icon'],
            ['id' => 10, 'name' => 'Glowing Heart', 'price' => 100, 'iconUrl' => 'https://api.iconify.design/lucide/heart.svg', 'type' => 'profile_icon'],
            ['id' => 11, 'name' => 'Ghostly Aura', 'price' => 200, 'iconUrl' => 'https://api.iconify.design/lucide/ghost.svg', 'type' => 'profile_icon'],
            ['id' => 12, 'name' => 'Crystal Gem', 'price' => 250, 'iconUrl' => 'https://api.iconify.design/lucide/diamond.svg', 'type' => 'profile_icon'],

            // Backgrounds (updated with actual image URLs)
            ['id' => 13, 'name' => 'Soft Gradient', 'price' => 300, 'iconUrl' => 'https://images.unsplash.com/photo-1557683316-973673baf926', 'type' => 'background'],
            ['id' => 14, 'name' => 'Starry Night', 'price' => 400, 'iconUrl' => 'https://images.unsplash.com/photo-1533206482744-2d8b72b7df4f', 'type' => 'background'],
            ['id' => 15, 'name' => 'Minimal Waves', 'price' => 350, 'iconUrl' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd66fbe', 'type' => 'background'],
            ['id' => 16, 'name' => 'Pastel Sky', 'price' => 350, 'iconUrl' => 'https://images.unsplash.com/photo-1499346030926-9a72daac6c63', 'type' => 'background'],
            ['id' => 17, 'name' => 'Urban Glow', 'price' => 400, 'iconUrl' => 'https://images.unsplash.com/photo-1519681393784-d120267933ba', 'type' => 'background'],

            // Animations (unchanged)
            ['id' => 18, 'name' => 'Gentle Sparkle', 'price' => 500, 'iconUrl' => 'https://api.iconify.design/lucide/sparkles.svg', 'type' => 'animation'],
            ['id' => 19, 'name' => 'Fading Pulse', 'price' => 550, 'iconUrl' => 'https://api.iconify.design/lucide/activity.svg', 'type' => 'animation'],
            ['id' => 20, 'name' => 'Soft Ripple', 'price' => 500, 'iconUrl' => 'https://api.iconify.design/lucide/waves.svg', 'type' => 'animation'],
            ['id' => 21, 'name' => 'Orbit Glow', 'price' => 600, 'iconUrl' => 'https://api.iconify.design/lucide/orbit.svg', 'type' => 'animation'],
            ['id' => 22, 'name' => 'Subtle Glitch', 'price' => 550, 'iconUrl' => 'https://api.iconify.design/lucide/scan-line.svg', 'type' => 'animation'],

            // Name Effects (unchanged)
            ['id' => 23, 'name' => 'Soft Glow', 'price' => 300, 'iconUrl' => 'https://api.iconify.design/lucide/lamp.svg', 'type' => 'name_effect'],
            ['id' => 24, 'name' => 'Gradient Fade', 'price' => 350, 'iconUrl' => 'https://api.iconify.design/lucide/blend.svg', 'type' => 'name_effect'],
            ['id' => 25, 'name' => 'Golden Outline', 'price' => 400, 'iconUrl' => 'https://api.iconify.design/lucide/badge.svg', 'type' => 'name_effect'],
            ['id' => 26, 'name' => 'Deep Shadow', 'price' => 300, 'iconUrl' => 'https://api.iconify.design/lucide/shadow.svg', 'type' => 'name_effect'],
            ['id' => 27, 'name' => 'Cosmic Shine', 'price' => 450, 'iconUrl' => 'https://api.iconify.design/lucide/stars.svg', 'type' => 'name_effect'],

            // Profile Frames (unchanged)
            ['id' => 28, 'name' => 'Golden Ring', 'price' => 300, 'iconUrl' => 'https://api.iconify.design/lucide/circle.svg', 'type' => 'profile_frame'],
            ['id' => 29, 'name' => 'Crystal Edge', 'price' => 350, 'iconUrl' => 'https://api.iconify.design/lucide/diamond.svg', 'type' => 'profile_frame'],
            ['id' => 30, 'name' => 'Star Border', 'price' => 350, 'iconUrl' => 'https://api.iconify.design/lucide/star.svg', 'type' => 'profile_frame'],
            ['id' => 31, 'name' => 'Cloud Frame', 'price' => 300, 'iconUrl' => 'https://api.iconify.design/lucide/cloud.svg', 'type' => 'profile_frame'],
            ['id' => 32, 'name' => 'Tech Circuit', 'price' => 400, 'iconUrl' => 'https://api.iconify.design/lucide/cpu.svg', 'type' => 'profile_frame'],

            // Profile Badges (unchanged)
            ['id' => 33, 'name' => 'Verified Badge', 'price' => 200, 'iconUrl' => 'https://api.iconify.design/lucide/check-circle.svg', 'type' => 'badge'],
            ['id' => 34, 'name' => 'Founder Badge', 'price' => 300, 'iconUrl' => 'https://api.iconify.design/lucide/award.svg', 'type' => 'badge'],
            ['id' => 35, 'name' => 'VIP Badge', 'price' => 250, 'iconUrl' => 'https://api.iconify.design/lucide/crown.svg', 'type' => 'badge'],
            ['id' => 36, 'name' => 'Creator Badge', 'price' => 200, 'iconUrl' => 'https://api.iconify.design/lucide/pen-tool.svg', 'type' => 'badge'],
            ['id' => 37, 'name' => 'Explorer Badge', 'price' => 200, 'iconUrl' => 'https://api.iconify.design/lucide/compass.svg', 'type' => 'badge'],
        ];

        DB::table('items')->insert($items);
    }
}
