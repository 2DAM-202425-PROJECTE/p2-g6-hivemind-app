<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsTableSeeder extends Seeder
{
    public function run()
    {
        // Truncate the items table
        DB::table('items')->truncate();

        // Define the items
        $items = [
            // Subscriptions (unchanged)
            ['id' => 1, 'name' => 'Basic', 'price' => 0, 'iconUrl' => 'https://api.iconify.design/lucide/sprout.svg'],
            ['id' => 2, 'name' => 'Premium', 'price' => 9.99, 'iconUrl' => 'https://api.iconify.design/lucide/shield-check.svg'],
            ['id' => 3, 'name' => 'Elite', 'price' => 19.99, 'iconUrl' => 'https://api.iconify.design/lucide/crown.svg'],

            // Credit Packs (unchanged)
            ['id' => 4, 'name' => '100 Credits', 'price' => 1.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg'],
            ['id' => 5, 'name' => '500 Credits', 'price' => 7.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg'],
            ['id' => 6, 'name' => '1000 Credits', 'price' => 14.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg'],
            ['id' => 7, 'name' => '2500 Credits', 'price' => 34.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg'],

            // Profile Icons (unchanged)
            ['id' => 8, 'name' => 'Mini Crown', 'price' => 150, 'iconUrl' => 'https://api.iconify.design/lucide/crown.svg'],
            ['id' => 9, 'name' => 'Shining Star', 'price' => 100, 'iconUrl' => 'https://api.iconify.design/lucide/star.svg'],
            ['id' => 10, 'name' => 'Glowing Heart', 'price' => 100, 'iconUrl' => 'https://api.iconify.design/lucide/heart.svg'],
            ['id' => 11, 'name' => 'Ghostly Aura', 'price' => 200, 'iconUrl' => 'https://api.iconify.design/lucide/ghost.svg'],
            ['id' => 12, 'name' => 'Crystal Gem', 'price' => 250, 'iconUrl' => 'https://api.iconify.design/lucide/diamond.svg'],

            // Backgrounds (unchanged)
            ['id' => 13, 'name' => 'Soft Gradient', 'price' => 300, 'iconUrl' => 'https://api.iconify.design/lucide/blend.svg'],
            ['id' => 14, 'name' => 'Starry Night', 'price' => 400, 'iconUrl' => 'https://api.iconify.design/lucide/stars.svg'],
            ['id' => 15, 'name' => 'Minimal Waves', 'price' => 350, 'iconUrl' => 'https://api.iconify.design/lucide/waves.svg'],
            ['id' => 16, 'name' => 'Pastel Sky', 'price' => 350, 'iconUrl' => 'https://api.iconify.design/lucide/cloud.svg'],
            ['id' => 17, 'name' => 'Urban Glow', 'price' => 400, 'iconUrl' => 'https://api.iconify.design/lucide/building-2.svg'],

            // Animations (unchanged)
            ['id' => 18, 'name' => 'Gentle Sparkle', 'price' => 500, 'iconUrl' => 'https://api.iconify.design/lucide/sparkles.svg'],
            ['id' => 19, 'name' => 'Fading Pulse', 'price' => 550, 'iconUrl' => 'https://api.iconify.design/lucide/activity.svg'],
            ['id' => 20, 'name' => 'Soft Ripple', 'price' => 500, 'iconUrl' => 'https://api.iconify.design/lucide/waves.svg'],
            ['id' => 21, 'name' => 'Orbit Glow', 'price' => 600, 'iconUrl' => 'https://api.iconify.design/lucide/orbit.svg'],
            ['id' => 22, 'name' => 'Subtle Glitch', 'price' => 550, 'iconUrl' => 'https://api.iconify.design/lucide/scan-line.svg'],

            // Name Effects (updated Shadow Drop for better visibility)
            ['id' => 23, 'name' => 'Soft Glow', 'price' => 300, 'iconUrl' => 'https://api.iconify.design/lucide/lamp.svg'],
            ['id' => 24, 'name' => 'Gradient Fade', 'price' => 350, 'iconUrl' => 'https://api.iconify.design/lucide/blend.svg'],
            ['id' => 25, 'name' => 'Golden Outline', 'price' => 400, 'iconUrl' => 'https://api.iconify.design/lucide/badge.svg'],
            ['id' => 26, 'name' => 'Deep Shadow', 'price' => 300, 'iconUrl' => 'https://api.iconify.design/lucide/shadow.svg'], // Renamed Shadow Drop to Deep Shadow
            ['id' => 27, 'name' => 'Cosmic Shine', 'price' => 450, 'iconUrl' => 'https://api.iconify.design/lucide/stars.svg'],

            // Profile Frames (unchanged)
            ['id' => 28, 'name' => 'Golden Ring', 'price' => 300, 'iconUrl' => 'https://api.iconify.design/lucide/circle.svg'],
            ['id' => 29, 'name' => 'Crystal Edge', 'price' => 350, 'iconUrl' => 'https://api.iconify.design/lucide/diamond.svg'],
            ['id' => 30, 'name' => 'Star Border', 'price' => 350, 'iconUrl' => 'https://api.iconify.design/lucide/star.svg'],
            ['id' => 31, 'name' => 'Cloud Frame', 'price' => 300, 'iconUrl' => 'https://api.iconify.design/lucide/cloud.svg'],
            ['id' => 32, 'name' => 'Tech Circuit', 'price' => 400, 'iconUrl' => 'https://api.iconify.design/lucide/cpu.svg'],

            // New Category: Profile Badges (small badges to display next to the profile picture or username)
            ['id' => 33, 'name' => 'Verified Badge', 'price' => 200, 'iconUrl' => 'https://api.iconify.design/lucide/check-circle.svg'],
            ['id' => 34, 'name' => 'Founder Badge', 'price' => 300, 'iconUrl' => 'https://api.iconify.design/lucide/award.svg'],
            ['id' => 35, 'name' => 'VIP Badge', 'price' => 250, 'iconUrl' => 'https://api.iconify.design/lucide/crown.svg'],
            ['id' => 36, 'name' => 'Creator Badge', 'price' => 200, 'iconUrl' => 'https://api.iconify.design/lucide/pen-tool.svg'],
            ['id' => 37, 'name' => 'Explorer Badge', 'price' => 200, 'iconUrl' => 'https://api.iconify.design/lucide/compass.svg'],
        ];

        DB::table('items')->insert($items);
    }
}
