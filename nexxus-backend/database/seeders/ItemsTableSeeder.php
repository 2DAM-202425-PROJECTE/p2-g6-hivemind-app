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
            // Subscriptions (3 items, unchanged)
            ['id' => 1, 'name' => 'Basic', 'price' => 0, 'iconUrl' => 'https://api.iconify.design/lucide/sprout.svg', 'type' => 'subscription'],
            ['id' => 2, 'name' => 'Premium', 'price' => 9.99, 'iconUrl' => 'https://api.iconify.design/lucide/shield-check.svg', 'type' => 'subscription'],
            ['id' => 3, 'name' => 'Elite', 'price' => 19.99, 'iconUrl' => 'https://api.iconify.design/lucide/crown.svg', 'type' => 'subscription'],

            // Credit Packs (8 items, unchanged)
            ['id' => 4, 'name' => '100 Credits', 'price' => 1.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack'],
            ['id' => 5, 'name' => '500 Credits', 'price' => 7.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack'],
            ['id' => 6, 'name' => '1000 Credits', 'price' => 14.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack'],
            ['id' => 7, 'name' => '2500 Credits', 'price' => 34.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack'],
            ['id' => 8, 'name' => '5000 Credits', 'price' => 59.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack'],
            ['id' => 9, 'name' => '200 Credits', 'price' => 3.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack'],
            ['id' => 10, 'name' => '750 Credits', 'price' => 11.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack'],
            ['id' => 11, 'name' => '1500 Credits', 'price' => 24.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack'],

            // Profile Icons (16 items: 8 original + 8 new)
            ['id' => 12, 'name' => 'Mini Crown', 'price' => 150, 'iconUrl' => 'https://api.iconify.design/lucide/crown.svg', 'type' => 'profile_icon'],
            ['id' => 13, 'name' => 'Shining Star', 'price' => 100, 'iconUrl' => 'https://api.iconify.design/lucide/star.svg', 'type' => 'profile_icon'],
            ['id' => 14, 'name' => 'Glowing Heart', 'price' => 100, 'iconUrl' => 'https://api.iconify.design/lucide/heart.svg', 'type' => 'profile_icon'],
            ['id' => 15, 'name' => 'Ghostly Aura', 'price' => 200, 'iconUrl' => 'https://api.iconify.design/lucide/ghost.svg', 'type' => 'profile_icon'],
            ['id' => 16, 'name' => 'Crystal Gem', 'price' => 250, 'iconUrl' => 'https://api.iconify.design/lucide/diamond.svg', 'type' => 'profile_icon'],
            ['id' => 17, 'name' => 'Thunder Bolt', 'price' => 150, 'iconUrl' => 'https://api.iconify.design/lucide/zap.svg', 'type' => 'profile_icon'],
            ['id' => 18, 'name' => 'Moon Glow', 'price' => 120, 'iconUrl' => 'https://api.iconify.design/lucide/moon.svg', 'type' => 'profile_icon'],
            ['id' => 19, 'name' => 'Sun Flare', 'price' => 130, 'iconUrl' => 'https://api.iconify.design/lucide/sun.svg', 'type' => 'profile_icon'],
            ['id' => 60, 'name' => 'Flame Crest', 'price' => 140, 'iconUrl' => 'https://api.iconify.design/lucide/flame.svg', 'type' => 'profile_icon'],
            ['id' => 61, 'name' => 'Snowflake Spark', 'price' => 110, 'iconUrl' => 'https://api.iconify.design/lucide/snowflake.svg', 'type' => 'profile_icon'],
            ['id' => 62, 'name' => 'Leaf Whisper', 'price' => 120, 'iconUrl' => 'https://api.iconify.design/lucide/leaf.svg', 'type' => 'profile_icon'],
            ['id' => 63, 'name' => 'Wave Ripple', 'price' => 130, 'iconUrl' => 'https://api.iconify.design/lucide/waves.svg', 'type' => 'profile_icon'],
            ['id' => 64, 'name' => 'Cloud Drift', 'price' => 150, 'iconUrl' => 'https://api.iconify.design/lucide/cloud.svg', 'type' => 'profile_icon'],
            ['id' => 65, 'name' => 'Gear Spin', 'price' => 160, 'iconUrl' => 'https://api.iconify.design/lucide/cog.svg', 'type' => 'profile_icon'],
            ['id' => 66, 'name' => 'Anchor Drop', 'price' => 140, 'iconUrl' => 'https://api.iconify.design/lucide/anchor.svg', 'type' => 'profile_icon'],
            ['id' => 67, 'name' => 'Feather Light', 'price' => 110, 'iconUrl' => 'https://api.iconify.design/lucide/feather.svg', 'type' => 'profile_icon'],

            // Backgrounds (16 items: 8 original + 8 new, all 4K from Unsplash)
            ['id' => 20, 'name' => 'Soft Gradient', 'price' => 300, 'iconUrl' => 'https://images.unsplash.com/photo-1557683316-973673baf926?w=3840&h=2160&fit=crop', 'type' => 'background'],
            ['id' => 21, 'name' => 'Starry Night', 'price' => 400, 'iconUrl' => 'https://images.unsplash.com/photo-1579546929518-9e396f3cc809?w=3840&h=2160&fit=crop', 'type' => 'background'],
            ['id' => 22, 'name' => 'Minimal Waves', 'price' => 350, 'iconUrl' => 'https://images.unsplash.com/photo-1558591710-4b4a1ae0f04d?w=3840&h=2160&fit=crop', 'type' => 'background'],
            ['id' => 23, 'name' => 'Pastel Sky', 'price' => 350, 'iconUrl' => 'https://images.unsplash.com/photo-1499346030926-9a72daac6c63?w=3840&h=2160&fit=crop', 'type' => 'background'],
            ['id' => 24, 'name' => 'Urban Glow', 'price' => 400, 'iconUrl' => 'https://images.unsplash.com/photo-1519681393784-d120267933ba?w=3840&h=2160&fit=crop', 'type' => 'background'],
            ['id' => 25, 'name' => 'Forest Mist', 'price' => 300, 'iconUrl' => 'https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?w=3840&h=2160&fit=crop', 'type' => 'background'],
            ['id' => 26, 'name' => 'Ocean Depth', 'price' => 350, 'iconUrl' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=3840&h=2160&fit=crop', 'type' => 'background'],
            ['id' => 27, 'name' => 'Desert Dunes', 'price' => 320, 'iconUrl' => 'https://images.unsplash.com/photo-1503435980610-a51f3ddfee50?w=3840&h=2160&fit=crop', 'type' => 'background'],
            ['id' => 68, 'name' => 'Mountain Peak', 'price' => 380, 'iconUrl' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=3840&h=2160&fit=crop', 'type' => 'background'],
            ['id' => 69, 'name' => 'Aurora Veil', 'price' => 450, 'iconUrl' => 'https://images.unsplash.com/photo-1572017194838-5a665e45e2ff?w=3840&h=2160&fit=crop', 'type' => 'background'],
            ['id' => 70, 'name' => 'Lush Valley', 'price' => 340, 'iconUrl' => 'https://images.unsplash.com/photo-1472214103451-9374bd1c798e?w=3840&h=2160&fit=crop', 'type' => 'background'],
            ['id' => 71, 'name' => 'Twilight City', 'price' => 410, 'iconUrl' => 'https://images.unsplash.com/photo-1503428593587-593da4f8e398?w=3840&h=2160&fit=crop', 'type' => 'background'],
            ['id' => 72, 'name' => 'Golden Fields', 'price' => 330, 'iconUrl' => 'https://images.unsplash.com/photo-1501696461415-6bd6660c6742?w=3840&h=2160&fit=crop', 'type' => 'background'],
            ['id' => 73, 'name' => 'Icy Plains', 'price' => 360, 'iconUrl' => 'https://images.unsplash.com/photo-1485286166769-36b57196888f?w=3840&h=2160&fit=crop', 'type' => 'background'],
            ['id' => 74, 'name' => 'Volcanic Ash', 'price' => 390, 'iconUrl' => 'https://images.unsplash.com/photo-1528164344705-47542687000d?w=3840&h=2160&fit=crop', 'type' => 'background'],
            ['id' => 75, 'name' => 'Cosmic Dust', 'price' => 420, 'iconUrl' => 'https://images.unsplash.com/photo-1447433693259-c8549e352d2d?w=3840&h=2160&fit=crop', 'type' => 'background'],

            // Animations (16 items: 8 original + 8 new)
            ['id' => 28, 'name' => 'Gentle Sparkle', 'price' => 500, 'iconUrl' => 'https://api.iconify.design/lucide/sparkles.svg', 'type' => 'animation'],
            ['id' => 29, 'name' => 'Fading Pulse', 'price' => 550, 'iconUrl' => 'https://api.iconify.design/lucide/activity.svg', 'type' => 'animation'],
            ['id' => 30, 'name' => 'Soft Ripple', 'price' => 500, 'iconUrl' => 'https://api.iconify.design/lucide/waves.svg', 'type' => 'animation'],
            ['id' => 31, 'name' => 'Orbit Glow', 'price' => 600, 'iconUrl' => 'https://api.iconify.design/lucide/orbit.svg', 'type' => 'animation'],
            ['id' => 32, 'name' => 'Subtle Glitch', 'price' => 550, 'iconUrl' => 'https://api.iconify.design/lucide/scan-line.svg', 'type' => 'animation'],
            ['id' => 33, 'name' => 'Twirl Flash', 'price' => 520, 'iconUrl' => 'https://api.iconify.design/lucide/refresh-cw.svg', 'type' => 'animation'],
            ['id' => 34, 'name' => 'Pulse Wave', 'price' => 530, 'iconUrl' => 'https://api.iconify.design/lucide/radio.svg', 'type' => 'animation'],
            ['id' => 35, 'name' => 'Star Burst', 'price' => 580, 'iconUrl' => 'https://api.iconify.design/lucide/sparkle.svg', 'type' => 'animation'],
            ['id' => 76, 'name' => 'Meteor Shower', 'price' => 590, 'iconUrl' => 'https://api.iconify.design/lucide/shooting-star.svg', 'type' => 'animation'],
            ['id' => 77, 'name' => 'Vortex Spin', 'price' => 560, 'iconUrl' => 'https://api.iconify.design/lucide/wind.svg', 'type' => 'animation'],
            ['id' => 78, 'name' => 'Flame Dance', 'price' => 540, 'iconUrl' => 'https://api.iconify.design/lucide/flame.svg', 'type' => 'animation'],
            ['id' => 79, 'name' => 'Frost Swirl', 'price' => 510, 'iconUrl' => 'https://api.iconify.design/lucide/snowflake.svg', 'type' => 'animation'],
            ['id' => 80, 'name' => 'Electric Surge', 'price' => 570, 'iconUrl' => 'https://api.iconify.design/lucide/zap.svg', 'type' => 'animation'],
            ['id' => 81, 'name' => 'Shadow Fade', 'price' => 520, 'iconUrl' => 'https://api.iconify.design/lucide/shadow.svg', 'type' => 'animation'],
            ['id' => 82, 'name' => 'Rainbow Pulse', 'price' => 600, 'iconUrl' => 'https://api.iconify.design/lucide/rainbow.svg', 'type' => 'animation'],
            ['id' => 83, 'name' => 'Bubble Pop', 'price' => 530, 'iconUrl' => 'https://api.iconify.design/lucide/droplets.svg', 'type' => 'animation'],

            // Name Effects (16 items: 8 original + 8 new)
            ['id' => 36, 'name' => 'Soft Glow', 'price' => 300, 'iconUrl' => 'https://api.iconify.design/lucide/lamp.svg', 'type' => 'name_effect'],
            ['id' => 37, 'name' => 'Gradient Fade', 'price' => 350, 'iconUrl' => 'https://api.iconify.design/lucide/blend.svg', 'type' => 'name_effect'],
            ['id' => 38, 'name' => 'Golden Outline', 'price' => 400, 'iconUrl' => 'https://api.iconify.design/lucide/badge.svg', 'type' => 'name_effect'],
            ['id' => 39, 'name' => 'Dark Pulse', 'price' => 300, 'iconUrl' => 'https://api.iconify.design/lucide/vibrate.svg', 'type' => 'name_effect'],
            ['id' => 40, 'name' => 'Cosmic Shine', 'price' => 450, 'iconUrl' => 'https://api.iconify.design/lucide/stars.svg', 'type' => 'name_effect'],
            ['id' => 41, 'name' => 'Neon Edge', 'price' => 320, 'iconUrl' => 'https://api.iconify.design/lucide/lightbulb.svg', 'type' => 'name_effect'],
            ['id' => 42, 'name' => 'Frost Glow', 'price' => 340, 'iconUrl' => 'https://api.iconify.design/lucide/snowflake.svg', 'type' => 'name_effect'],
            ['id' => 43, 'name' => 'Fire Flicker', 'price' => 360, 'iconUrl' => 'https://api.iconify.design/lucide/flame.svg', 'type' => 'name_effect'],
            ['id' => 84, 'name' => 'Emerald Sheen', 'price' => 370, 'iconUrl' => 'https://api.iconify.design/lucide/gem.svg', 'type' => 'name_effect'],
            ['id' => 85, 'name' => 'Shadow Drift', 'price' => 310, 'iconUrl' => 'https://api.iconify.design/lucide/shadow.svg', 'type' => 'name_effect'],
            ['id' => 86, 'name' => 'Electric Glow', 'price' => 380, 'iconUrl' => 'https://api.iconify.design/lucide/zap.svg', 'type' => 'name_effect'],
            ['id' => 87, 'name' => 'Lunar Haze', 'price' => 330, 'iconUrl' => 'https://api.iconify.design/lucide/moon.svg', 'type' => 'name_effect'],
            ['id' => 88, 'name' => 'Solar Flare', 'price' => 390, 'iconUrl' => 'https://api.iconify.design/lucide/sun.svg', 'type' => 'name_effect'],
            ['id' => 89, 'name' => 'Wave Shimmer', 'price' => 340, 'iconUrl' => 'https://api.iconify.design/lucide/waves.svg', 'type' => 'name_effect'],
            ['id' => 90, 'name' => 'Crystal Pulse', 'price' => 400, 'iconUrl' => 'https://api.iconify.design/lucide/diamond.svg', 'type' => 'name_effect'],
            ['id' => 91, 'name' => 'Rainbow Gleam', 'price' => 420, 'iconUrl' => 'https://api.iconify.design/lucide/rainbow.svg', 'type' => 'name_effect'],

            // Profile Frames (16 items: 8 original + 8 new)
            ['id' => 44, 'name' => 'Golden Ring', 'price' => 300, 'iconUrl' => 'https://api.iconify.design/lucide/circle.svg', 'type' => 'profile_frame'],
            ['id' => 45, 'name' => 'Crystal Edge', 'price' => 350, 'iconUrl' => 'https://api.iconify.design/lucide/diamond.svg', 'type' => 'profile_frame'],
            ['id' => 46, 'name' => 'Star Border', 'price' => 350, 'iconUrl' => 'https://api.iconify.design/lucide/star.svg', 'type' => 'profile_frame'],
            ['id' => 47, 'name' => 'Cloud Frame', 'price' => 300, 'iconUrl' => 'https://api.iconify.design/lucide/cloud.svg', 'type' => 'profile_frame'],
            ['id' => 48, 'name' => 'Tech Circuit', 'price' => 400, 'iconUrl' => 'https://api.iconify.design/lucide/cpu.svg', 'type' => 'profile_frame'],
            ['id' => 49, 'name' => 'Leaf Wreath', 'price' => 320, 'iconUrl' => 'https://api.iconify.design/lucide/leaf.svg', 'type' => 'profile_frame'],
            ['id' => 50, 'name' => 'Wave Crest', 'price' => 340, 'iconUrl' => 'https://api.iconify.design/lucide/waves.svg', 'type' => 'profile_frame'],
            ['id' => 51, 'name' => 'Pixel Grid', 'price' => 360, 'iconUrl' => 'https://api.iconify.design/lucide/grid.svg', 'type' => 'profile_frame'],
            ['id' => 92, 'name' => 'Flame Halo', 'price' => 370, 'iconUrl' => 'https://api.iconify.design/lucide/flame.svg', 'type' => 'profile_frame'],
            ['id' => 93, 'name' => 'Frost Ring', 'price' => 330, 'iconUrl' => 'https://api.iconify.design/lucide/snowflake.svg', 'type' => 'profile_frame'],
            ['id' => 94, 'name' => 'Gear Frame', 'price' => 380, 'iconUrl' => 'https://api.iconify.design/lucide/cog.svg', 'type' => 'profile_frame'],
            ['id' => 95, 'name' => 'Moon Orbit', 'price' => 340, 'iconUrl' => 'https://api.iconify.design/lucide/moon.svg', 'type' => 'profile_frame'],
            ['id' => 96, 'name' => 'Sun Burst', 'price' => 390, 'iconUrl' => 'https://api.iconify.design/lucide/sun.svg', 'type' => 'profile_frame'],
            ['id' => 97, 'name' => 'Vine Wrap', 'price' => 320, 'iconUrl' => 'https://api.iconify.design/lucide/vine.svg', 'type' => 'profile_frame'],
            ['id' => 98, 'name' => 'Neon Circuit', 'price' => 400, 'iconUrl' => 'https://api.iconify.design/lucide/circuit-board.svg', 'type' => 'profile_frame'],
            ['id' => 99, 'name' => 'Starfield Edge', 'price' => 410, 'iconUrl' => 'https://api.iconify.design/lucide/stars.svg', 'type' => 'profile_frame'],

            // Profile Badges (16 items: 8 original + 8 new)
            ['id' => 52, 'name' => 'Verified Badge', 'price' => 200, 'iconUrl' => 'https://api.iconify.design/lucide/check-circle.svg', 'type' => 'badge'],
            ['id' => 53, 'name' => 'Founder Badge', 'price' => 300, 'iconUrl' => 'https://api.iconify.design/lucide/award.svg', 'type' => 'badge'],
            ['id' => 54, 'name' => 'VIP Badge', 'price' => 250, 'iconUrl' => 'https://api.iconify.design/lucide/crown.svg', 'type' => 'badge'],
            ['id' => 55, 'name' => 'Creator Badge', 'price' => 200, 'iconUrl' => 'https://api.iconify.design/lucide/pen-tool.svg', 'type' => 'badge'],
            ['id' => 56, 'name' => 'Explorer Badge', 'price' => 200, 'iconUrl' => 'https://api.iconify.design/lucide/compass.svg', 'type' => 'badge'],
            ['id' => 57, 'name' => 'Legend Badge', 'price' => 280, 'iconUrl' => 'https://api.iconify.design/lucide/trophy.svg', 'type' => 'badge'],
            ['id' => 58, 'name' => 'Pioneer Badge', 'price' => 260, 'iconUrl' => 'https://api.iconify.design/lucide/flag.svg', 'type' => 'badge'],
            ['id' => 59, 'name' => 'Guardian Badge', 'price' => 240, 'iconUrl' => 'https://api.iconify.design/lucide/shield.svg', 'type' => 'badge'],
            ['id' => 100, 'name' => 'Warrior Badge', 'price' => 270, 'iconUrl' => 'https://api.iconify.design/lucide/sword.svg', 'type' => 'badge'],
            ['id' => 101, 'name' => 'Sage Badge', 'price' => 230, 'iconUrl' => 'https://api.iconify.design/lucide/book-open.svg', 'type' => 'badge'],
            ['id' => 102, 'name' => 'Star Gazer Badge', 'price' => 250, 'iconUrl' => 'https://api.iconify.design/lucide/stars.svg', 'type' => 'badge'],
            ['id' => 103, 'name' => 'Trailblazer Badge', 'price' => 290, 'iconUrl' => 'https://api.iconify.design/lucide/footprints.svg', 'type' => 'badge'],
            ['id' => 104, 'name' => 'Elementalist Badge', 'price' => 260, 'iconUrl' => 'https://api.iconify.design/lucide/droplet.svg', 'type' => 'badge'],
            ['id' => 105, 'name' => 'Innovator Badge', 'price' => 280, 'iconUrl' => 'https://api.iconify.design/lucide/lightbulb.svg', 'type' => 'badge'],
            ['id' => 106, 'name' => 'Nomad Badge', 'price' => 240, 'iconUrl' => 'https://api.iconify.design/lucide/map.svg', 'type' => 'badge'],
            ['id' => 107, 'name' => 'Champion Badge', 'price' => 300, 'iconUrl' => 'https://api.iconify.design/lucide/medal.svg', 'type' => 'badge'],
        ];

        DB::table('items')->insert($items);
    }
}
