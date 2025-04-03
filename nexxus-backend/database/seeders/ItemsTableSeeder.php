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
            // Add subscription tiers
            ['id' => 1, 'name' => 'Basic', 'price' => 0, 'iconUrl' => 'https://api.iconify.design/lucide/sprout.svg'],
            ['id' => 2, 'name' => 'Standard Premium', 'price' => 10, 'iconUrl' => 'https://api.iconify.design/lucide/shield.svg'],
            ['id' => 3, 'name' => 'Ultimate Premium', 'price' => 20, 'iconUrl' => 'https://api.iconify.design/lucide/trophy.svg'],
            // Add credit packs
            ['id' => 4, 'name' => '100 Credits', 'price' => 2, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg'],
            ['id' => 5, 'name' => '500 Credits', 'price' => 8, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg'],
            ['id' => 6, 'name' => '1000 Credits', 'price' => 15, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg'],
            ['id' => 7, 'name' => '5000 Credits', 'price' => 60, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg'],
            ['id' => 8, 'name' => 'Crown Icon', 'price' => 250, 'iconUrl' => 'https://api.iconify.design/lucide/crown.svg'],
            ['id' => 9, 'name' => 'Star Icon', 'price' => 150, 'iconUrl' => 'https://api.iconify.design/lucide/star.svg'],
            ['id' => 10, 'name' => 'Heart Icon', 'price' => 100, 'iconUrl' => 'https://api.iconify.design/lucide/heart.svg'],
            ['id' => 11, 'name' => 'Ghost Icon', 'price' => 200, 'iconUrl' => 'https://api.iconify.design/lucide/ghost.svg'],
            ['id' => 12, 'name' => 'Diamond Icon', 'price' => 300, 'iconUrl' => 'https://api.iconify.design/lucide/diamond.svg'],
            ['id' => 13, 'name' => 'Lightning Icon', 'price' => 200, 'iconUrl' => 'https://api.iconify.design/lucide/zap.svg'],
            ['id' => 14, 'name' => 'Fire Icon', 'price' => 200, 'iconUrl' => 'https://api.iconify.design/lucide/flame.svg'],
            ['id' => 15, 'name' => 'Music Icon', 'price' => 150, 'iconUrl' => 'https://api.iconify.design/lucide/music.svg'],
            ['id' => 16, 'name' => 'Galaxy', 'price' => 500, 'iconUrl' => 'https://api.iconify.design/lucide/sparkles.svg'],
            ['id' => 17, 'name' => 'Night City', 'price' => 400, 'iconUrl' => 'https://api.iconify.design/lucide/building-2.svg'],
            ['id' => 18, 'name' => 'Mountain', 'price' => 300, 'iconUrl' => 'https://api.iconify.design/lucide/mountain.svg'],
            ['id' => 19, 'name' => 'Blossom', 'price' => 350, 'iconUrl' => 'https://api.iconify.design/lucide/flower-2.svg'],
            ['id' => 20, 'name' => 'Ocean', 'price' => 450, 'iconUrl' => 'https://api.iconify.design/lucide/waves.svg'],
            ['id' => 21, 'name' => 'Forest', 'price' => 400, 'iconUrl' => 'https://api.iconify.design/lucide/trees.svg'],
            ['id' => 22, 'name' => 'Desert', 'price' => 350, 'iconUrl' => 'https://api.iconify.design/lucide/sun.svg'],
            ['id' => 23, 'name' => 'Space', 'price' => 550, 'iconUrl' => 'https://api.iconify.design/lucide/moon.svg'],
            ['id' => 24, 'name' => 'Sparkle', 'price' => 600, 'iconUrl' => 'https://api.iconify.design/lucide/sparkles.svg'],
            ['id' => 25, 'name' => 'Wind', 'price' => 700, 'iconUrl' => 'https://api.iconify.design/lucide/wind.svg'],
            ['id' => 26, 'name' => 'Fire', 'price' => 800, 'iconUrl' => 'https://api.iconify.design/lucide/flame.svg'],
            ['id' => 27, 'name' => 'Wave', 'price' => 750, 'iconUrl' => 'https://api.iconify.design/lucide/waves.svg'],
            ['id' => 28, 'name' => 'Rainbow', 'price' => 900, 'iconUrl' => 'https://api.iconify.design/lucide/palette.svg'],
            ['id' => 29, 'name' => 'Glitch', 'price' => 850, 'iconUrl' => 'https://api.iconify.design/lucide/scan-line.svg'],
            ['id' => 30, 'name' => 'Pulse', 'price' => 700, 'iconUrl' => 'https://api.iconify.design/lucide/activity.svg'],
            ['id' => 31, 'name' => 'Orbit', 'price' => 800, 'iconUrl' => 'https://api.iconify.design/lucide/orbit.svg'],
            ['id' => 32, 'name' => 'Smile Emoji', 'price' => 50, 'iconUrl' => 'https://api.iconify.design/lucide/smile.svg'],
            ['id' => 33, 'name' => 'Laugh Emoji', 'price' => 50, 'iconUrl' => 'https://api.iconify.design/lucide/laugh.svg'],
            ['id' => 34, 'name' => 'Wink Emoji', 'price' => 50, 'iconUrl' => 'https://api.iconify.design/lucide/smile-plus.svg'],
            ['id' => 35, 'name' => 'Cool Emoji', 'price' => 50, 'iconUrl' => 'https://api.iconify.design/lucide/sun.svg'],
            ['id' => 36, 'name' => 'Party Emoji', 'price' => 75, 'iconUrl' => 'https://api.iconify.design/lucide/party-popper.svg'],
            ['id' => 37, 'name' => 'Love Emoji', 'price' => 75, 'iconUrl' => 'https://api.iconify.design/lucide/heart-handshake.svg'],
            ['id' => 38, 'name' => 'Surprised Emoji', 'price' => 50, 'iconUrl' => 'https://api.iconify.design/lucide/circle-dot.svg'],
            ['id' => 39, 'name' => 'Star Eyes Emoji', 'price' => 75, 'iconUrl' => 'https://api.iconify.design/lucide/star.svg'],
            ['id' => 40, 'name' => 'Gradient', 'price' => 400, 'iconUrl' => 'https://api.iconify.design/lucide/blend.svg'],
            ['id' => 41, 'name' => 'Neon', 'price' => 450, 'iconUrl' => 'https://api.iconify.design/lucide/lamp.svg'],
            ['id' => 42, 'name' => 'Gold', 'price' => 500, 'iconUrl' => 'https://api.iconify.design/lucide/badge.svg'],
            ['id' => 43, 'name' => 'Glitter', 'price' => 450, 'iconUrl' => 'https://api.iconify.design/lucide/sparkles.svg'],
            ['id' => 44, 'name' => 'Shadow', 'price' => 350, 'iconUrl' => 'https://api.iconify.design/lucide/box.svg'],
            ['id' => 45, 'name' => 'Pixel', 'price' => 400, 'iconUrl' => 'https://api.iconify.design/lucide/square.svg'],
            ['id' => 46, 'name' => 'Cosmic', 'price' => 500, 'iconUrl' => 'https://api.iconify.design/lucide/stars.svg'],
            ['id' => 47, 'name' => 'Golden Frame', 'price' => 300, 'iconUrl' => 'https://api.iconify.design/lucide/square.svg'],
            ['id' => 48, 'name' => 'Crystal Frame', 'price' => 350, 'iconUrl' => 'https://api.iconify.design/lucide/pentagon.svg'],
            ['id' => 49, 'name' => 'Flame Frame', 'price' => 400, 'iconUrl' => 'https://api.iconify.design/lucide/flame.svg'],
            ['id' => 50, 'name' => 'Nature Frame', 'price' => 300, 'iconUrl' => 'https://api.iconify.design/lucide/flower.svg'],
            ['id' => 51, 'name' => 'Tech Frame', 'price' => 350, 'iconUrl' => 'https://api.iconify.design/lucide/cpu.svg'],
            ['id' => 52, 'name' => 'Star Frame', 'price' => 400, 'iconUrl' => 'https://api.iconify.design/lucide/star.svg'],
            ['id' => 53, 'name' => 'Cloud Frame', 'price' => 300, 'iconUrl' => 'https://api.iconify.design/lucide/cloud.svg'],
            ['id' => 54, 'name' => 'Royal Frame', 'price' => 450, 'iconUrl' => 'https://api.iconify.design/lucide/crown.svg'],
        ];

        DB::table('items')->insert($items);
    }
}
