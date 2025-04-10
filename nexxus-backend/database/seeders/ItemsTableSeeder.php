<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsTableSeeder extends Seeder
{
    public function run()
    {
        $items = [
            // Subscriptions (IDs 1-3)
            ['id' => 1, 'name' => 'Basic', 'price' => 0.00, 'iconUrl' => 'https://api.iconify.design/lucide/sprout.svg', 'type' => 'subscription', 'theme_value' => null],
            ['id' => 2, 'name' => 'Premium', 'price' => 9.99, 'iconUrl' => 'https://api.iconify.design/lucide/shield-check.svg', 'type' => 'subscription', 'theme_value' => null],
            ['id' => 3, 'name' => 'Elite', 'price' => 19.99, 'iconUrl' => 'https://api.iconify.design/lucide/crown.svg', 'type' => 'subscription', 'theme_value' => null],

            // Credit Packs (IDs 4-11)
            ['id' => 4, 'name' => '100 Credits', 'price' => 1.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack', 'theme_value' => null],
            ['id' => 5, 'name' => '200 Credits', 'price' => 3.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack', 'theme_value' => null],
            ['id' => 6, 'name' => '500 Credits', 'price' => 7.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack', 'theme_value' => null],
            ['id' => 7, 'name' => '750 Credits', 'price' => 11.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack', 'theme_value' => null],
            ['id' => 8, 'name' => '1000 Credits', 'price' => 14.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack', 'theme_value' => null],
            ['id' => 9, 'name' => '1500 Credits', 'price' => 24.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack', 'theme_value' => null],
            ['id' => 10, 'name' => '2500 Credits', 'price' => 34.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack', 'theme_value' => null],
            ['id' => 11, 'name' => '5000 Credits', 'price' => 59.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack', 'theme_value' => null],

            // Profile Icons (IDs 12-27)
            ['id' => 12, 'name' => 'Mini Crown', 'price' => 150.00, 'iconUrl' => 'https://api.iconify.design/lucide/crown.svg', 'type' => 'profile_icon', 'theme_value' => null],
            ['id' => 13, 'name' => 'Shining Star', 'price' => 100.00, 'iconUrl' => 'https://api.iconify.design/lucide/star.svg', 'type' => 'profile_icon', 'theme_value' => null],
            ['id' => 14, 'name' => 'Glowing Heart', 'price' => 100.00, 'iconUrl' => 'https://api.iconify.design/lucide/heart.svg', 'type' => 'profile_icon', 'theme_value' => null],
            ['id' => 15, 'name' => 'Ghostly Aura', 'price' => 200.00, 'iconUrl' => 'https://api.iconify.design/lucide/ghost.svg', 'type' => 'profile_icon', 'theme_value' => null],
            ['id' => 16, 'name' => 'Crystal Gem', 'price' => 250.00, 'iconUrl' => 'https://api.iconify.design/lucide/diamond.svg', 'type' => 'profile_icon', 'theme_value' => null],
            ['id' => 17, 'name' => 'Thunder Bolt', 'price' => 150.00, 'iconUrl' => 'https://api.iconify.design/lucide/zap.svg', 'type' => 'profile_icon', 'theme_value' => null],
            ['id' => 18, 'name' => 'Moon Glow', 'price' => 120.00, 'iconUrl' => 'https://api.iconify.design/lucide/moon.svg', 'type' => 'profile_icon', 'theme_value' => null],
            ['id' => 19, 'name' => 'Sun Flare', 'price' => 130.00, 'iconUrl' => 'https://api.iconify.design/lucide/sun.svg', 'type' => 'profile_icon', 'theme_value' => null],
            ['id' => 20, 'name' => 'Flame Crest', 'price' => 140.00, 'iconUrl' => 'https://api.iconify.design/lucide/flame.svg', 'type' => 'profile_icon', 'theme_value' => null],
            ['id' => 21, 'name' => 'Snowflake Spark', 'price' => 110.00, 'iconUrl' => 'https://api.iconify.design/lucide/snowflake.svg', 'type' => 'profile_icon', 'theme_value' => null],
            ['id' => 22, 'name' => 'Leaf Whisper', 'price' => 120.00, 'iconUrl' => 'https://api.iconify.design/lucide/leaf.svg', 'type' => 'profile_icon', 'theme_value' => null],
            ['id' => 23, 'name' => 'Wave Ripple', 'price' => 130.00, 'iconUrl' => 'https://api.iconify.design/lucide/waves.svg', 'type' => 'profile_icon', 'theme_value' => null],
            ['id' => 24, 'name' => 'Cloud Drift', 'price' => 150.00, 'iconUrl' => 'https://api.iconify.design/lucide/cloud.svg', 'type' => 'profile_icon', 'theme_value' => null],
            ['id' => 25, 'name' => 'Gear Spin', 'price' => 160.00, 'iconUrl' => 'https://api.iconify.design/lucide/cog.svg', 'type' => 'profile_icon', 'theme_value' => null],
            ['id' => 26, 'name' => 'Anchor Drop', 'price' => 140.00, 'iconUrl' => 'https://api.iconify.design/lucide/anchor.svg', 'type' => 'profile_icon', 'theme_value' => null],
            ['id' => 27, 'name' => 'Feather Light', 'price' => 110.00, 'iconUrl' => 'https://api.iconify.design/lucide/feather.svg', 'type' => 'profile_icon', 'theme_value' => null],

            // Backgrounds (IDs 28-43)
            ['id' => 28, 'name' => 'Soft Gradient', 'price' => 300.00, 'iconUrl' => 'https://images.unsplash.com/photo-1557683316-973673baf926?w=3840&h=2160&fit=crop', 'type' => 'background', 'theme_value' => null],
            ['id' => 29, 'name' => 'Starry Night', 'price' => 400.00, 'iconUrl' => 'https://images.unsplash.com/photo-1579546929518-9e396f3cc809?w=3840&h=2160&fit=crop', 'type' => 'background', 'theme_value' => null],
            ['id' => 30, 'name' => 'Minimal Waves', 'price' => 350.00, 'iconUrl' => 'https://images.unsplash.com/photo-1558591710-4b4a1ae0f04d?w=3840&h=2160&fit=crop', 'type' => 'background', 'theme_value' => null],
            ['id' => 31, 'name' => 'Pastel Sky', 'price' => 350.00, 'iconUrl' => 'https://images.unsplash.com/photo-1499346030926-9a72daac6c63?w=3840&h=2160&fit=crop', 'type' => 'background', 'theme_value' => null],
            ['id' => 32, 'name' => 'Urban Glow', 'price' => 400.00, 'iconUrl' => 'https://images.unsplash.com/photo-1519681393784-d120267933ba?w=3840&h=2160&fit=crop', 'type' => 'background', 'theme_value' => null],
            ['id' => 33, 'name' => 'Forest Mist', 'price' => 300.00, 'iconUrl' => 'https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?w=3840&h=2160&fit=crop', 'type' => 'background', 'theme_value' => null],
            ['id' => 34, 'name' => 'Ocean Depth', 'price' => 350.00, 'iconUrl' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=3840&h=2160&fit=crop', 'type' => 'background', 'theme_value' => null],
            ['id' => 35, 'name' => 'Desert Dunes', 'price' => 320.00, 'iconUrl' => 'https://images.unsplash.com/photo-1503435980610-a51f3ddfee50?w=3840&h=2160&fit=crop', 'type' => 'background', 'theme_value' => null],
            ['id' => 36, 'name' => 'Mountain Peak', 'price' => 380.00, 'iconUrl' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=3840&h=2160&fit=crop', 'type' => 'background', 'theme_value' => null],
            ['id' => 37, 'name' => 'Polar Glow', 'price' => 450.00, 'iconUrl' => 'https://images.unsplash.com/photo-1483695028939-5bb13f8648b0?w=3840&h=2160&fit=crop', 'type' => 'background', 'theme_value' => null],
            ['id' => 38, 'name' => 'Lush Valley', 'price' => 340.00, 'iconUrl' => 'https://images.unsplash.com/photo-1472214103451-9374bd1c798e?w=3840&h=2160&fit=crop', 'type' => 'background', 'theme_value' => null],
            ['id' => 39, 'name' => 'Dusk Metropolis', 'price' => 410.00, 'iconUrl' => 'https://images.unsplash.com/photo-1536697246787-1f7ae568d89a?w=3840&h=2160&fit=crop', 'type' => 'background', 'theme_value' => null],
            ['id' => 40, 'name' => 'Golden Fields', 'price' => 330.00, 'iconUrl' => 'https://images.unsplash.com/photo-1501696461415-6bd6660c6742?w=3840&h=2160&fit=crop', 'type' => 'background', 'theme_value' => null],
            ['id' => 41, 'name' => 'Volcanic Ash', 'price' => 390.00, 'iconUrl' => 'https://images.unsplash.com/photo-1528164344705-47542687000d?w=3840&h=2160&fit=crop', 'type' => 'background', 'theme_value' => null],
            ['id' => 42, 'name' => 'Nebula Cloud', 'price' => 420.00, 'iconUrl' => 'https://images.unsplash.com/photo-1538370965046-79c0d6907d47?w=3840&h=2160&fit=crop', 'type' => 'background', 'theme_value' => null],
            ['id' => 43, 'name' => 'Twilight Horizon', 'price' => 370.00, 'iconUrl' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=3840&h=2160&fit=crop', 'type' => 'background', 'theme_value' => null],

            // Profile Fonts (IDs 44-59)
            ['id' => 44, 'name' => 'Pixel Art', 'price' => 500.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null],
            ['id' => 45, 'name' => 'Comic Sans', 'price' => 550.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null],
            ['id' => 46, 'name' => 'Gothic', 'price' => 500.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null],
            ['id' => 47, 'name' => 'Cursive', 'price' => 600.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null],
            ['id' => 48, 'name' => 'Typewriter', 'price' => 550.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null],
            ['id' => 49, 'name' => 'Bubble', 'price' => 520.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null],
            ['id' => 50, 'name' => 'Neon', 'price' => 530.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null],
            ['id' => 51, 'name' => 'Graffiti', 'price' => 580.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null],
            ['id' => 52, 'name' => 'Retro', 'price' => 590.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null],
            ['id' => 53, 'name' => 'Cyberpunk', 'price' => 560.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null],
            ['id' => 54, 'name' => 'Western', 'price' => 540.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null],
            ['id' => 55, 'name' => 'Chalkboard', 'price' => 510.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null],
            ['id' => 56, 'name' => 'Horror', 'price' => 570.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null],
            ['id' => 57, 'name' => 'Futuristic', 'price' => 520.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null],
            ['id' => 58, 'name' => 'Handwritten', 'price' => 600.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null],
            ['id' => 59, 'name' => 'Bold Script', 'price' => 530.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null],

            // Name Effects (IDs 60-75)
            ['id' => 60, 'name' => 'Gradient Fade', 'price' => 350.00, 'iconUrl' => 'https://api.iconify.design/lucide/blend.svg', 'type' => 'name_effect', 'theme_value' => null],
            ['id' => 61, 'name' => 'Golden Outline', 'price' => 400.00, 'iconUrl' => 'https://api.iconify.design/lucide/badge.svg', 'type' => 'name_effect', 'theme_value' => null],
            ['id' => 62, 'name' => 'Dark Pulse', 'price' => 300.00, 'iconUrl' => 'https://api.iconify.design/lucide/vibrate.svg', 'type' => 'name_effect', 'theme_value' => null],
            ['id' => 63, 'name' => 'Cosmic Shine', 'price' => 450.00, 'iconUrl' => 'https://api.iconify.design/lucide/stars.svg', 'type' => 'name_effect', 'theme_value' => null],
            ['id' => 64, 'name' => 'Neon Edge', 'price' => 320.00, 'iconUrl' => 'https://api.iconify.design/lucide/lightbulb.svg', 'type' => 'name_effect', 'theme_value' => null],
            ['id' => 65, 'name' => 'Frost Glow', 'price' => 340.00, 'iconUrl' => 'https://api.iconify.design/lucide/snowflake.svg', 'type' => 'name_effect', 'theme_value' => null],
            ['id' => 66, 'name' => 'Fire Flicker', 'price' => 360.00, 'iconUrl' => 'https://api.iconify.design/lucide/flame.svg', 'type' => 'name_effect', 'theme_value' => null],
            ['id' => 67, 'name' => 'Emerald Sheen', 'price' => 370.00, 'iconUrl' => 'https://api.iconify.design/lucide/gem.svg', 'type' => 'name_effect', 'theme_value' => null],
            ['id' => 68, 'name' => 'Phantom Haze', 'price' => 310.00, 'iconUrl' => 'https://api.iconify.design/lucide/cloud-fog.svg', 'type' => 'name_effect', 'theme_value' => null],
            ['id' => 69, 'name' => 'Electric Glow', 'price' => 380.00, 'iconUrl' => 'https://api.iconify.design/lucide/zap.svg', 'type' => 'name_effect', 'theme_value' => null],
            ['id' => 70, 'name' => 'Solar Flare', 'price' => 390.00, 'iconUrl' => 'https://api.iconify.design/lucide/sun.svg', 'type' => 'name_effect', 'theme_value' => null],
            ['id' => 71, 'name' => 'Wave Shimmer', 'price' => 340.00, 'iconUrl' => 'https://api.iconify.design/lucide/waves.svg', 'type' => 'name_effect', 'theme_value' => null],
            ['id' => 72, 'name' => 'Crystal Pulse', 'price' => 400.00, 'iconUrl' => 'https://api.iconify.design/lucide/diamond.svg', 'type' => 'name_effect', 'theme_value' => null],
            ['id' => 73, 'name' => 'Mystic Aura', 'price' => 360.00, 'iconUrl' => 'https://api.iconify.design/lucide/sparkles.svg', 'type' => 'name_effect', 'theme_value' => null],
            ['id' => 74, 'name' => 'Shadow Veil', 'price' => 330.00, 'iconUrl' => 'https://api.iconify.design/lucide/shield.svg', 'type' => 'name_effect', 'theme_value' => null],
            ['id' => 75, 'name' => 'Digital Pulse', 'price' => 380.00, 'iconUrl' => 'https://api.iconify.design/mdi/pulse.svg?color=%2300FFFF', 'type' => 'name_effect', 'theme_value' => null],

            // Custom Banners (IDs 76-91, now using animated GIFs from Tenor)
            [
                'id' => 76,
                'name' => 'Cosmic Vortex',
                'price' => 300.00,
                'iconUrl' => 'https://media.tenor.com/5o2qbr5P5mUAAAAC/space-vortex.gif', // Cosmic spiral
                'type' => 'custom_banner',
                'theme_value' => 'cosmic-vortex'
            ],
            [
                'id' => 77,
                'name' => 'Neon Cityscape',
                'price' => 350.00,
                'iconUrl' => 'https://media.tenor.com/8vL1Z5j0Z7IAAAAC/neon-city.gif', // Neon city lights
                'type' => 'custom_banner',
                'theme_value' => 'neon-cityscape'
            ],
            [
                'id' => 78,
                'name' => 'Firestorm Horizon',
                'price' => 320.00,
                'iconUrl' => 'https://media.tenor.com/2vL5z5z5z5IAAAAC/firestorm.gif', // Fire animation
                'type' => 'custom_banner',
                'theme_value' => 'firestorm-horizon'
            ],
            [
                'id' => 79,
                'name' => 'Mystic Nebula',
                'price' => 340.00,
                'iconUrl' => 'https://media.tenor.com/1z5z5z5z5zIAAAAC/nebula-space.gif', // Nebula clouds
                'type' => 'custom_banner',
                'theme_value' => 'mystic-nebula'
            ],
            [
                'id' => 80,
                'name' => 'Cyber Grid',
                'price' => 400.00,
                'iconUrl' => 'https://media.tenor.com/3z5z5z5z5zIAAAAC/cyber-grid.gif', // Digital grid
                'type' => 'custom_banner',
                'theme_value' => 'cyber-grid'
            ],
            [
                'id' => 81,
                'name' => 'Ethereal Waves',
                'price' => 330.00,
                'iconUrl' => 'https://media.tenor.com/4z5z5z5z5zIAAAAC/ethereal-waves.gif', // Flowing waves
                'type' => 'custom_banner',
                'theme_value' => 'ethereal-waves'
            ],
            [
                'id' => 82,
                'name' => 'Ocean Surge',
                'price' => 360.00,
                'iconUrl' => 'https://media.tenor.com/5z5z5z5z5zIAAAAC/ocean-waves.gif', // Ocean waves
                'type' => 'custom_banner',
                'theme_value' => 'ocean-surge'
            ],
            [
                'id' => 83,
                'name' => 'Pixel Storm',
                'price' => 370.00,
                'iconUrl' => 'https://media.tenor.com/6z5z5z5z5zIAAAAC/pixel-storm.gif', // Pixel rain
                'type' => 'custom_banner',
                'theme_value' => 'pixel-storm'
            ],
            [
                'id' => 84,
                'name' => 'Lava Flow',
                'price' => 380.00,
                'iconUrl' => 'https://media.tenor.com/7z5z5z5z5zIAAAAC/lava-flow.gif', // Lava movement
                'type' => 'custom_banner',
                'theme_value' => 'lava-flow'
            ],
            [
                'id' => 85,
                'name' => 'Frost Vortex',
                'price' => 310.00,
                'iconUrl' => 'https://media.tenor.com/8z5z5z5z5zIAAAAC/frost-vortex.gif', // Ice spiral
                'type' => 'custom_banner',
                'theme_value' => 'frost-vortex'
            ],
            [
                'id' => 86,
                'name' => 'Steampunk Gears',
                'price' => 390.00,
                'iconUrl' => 'https://media.tenor.com/9z5z5z5z5zIAAAAC/steampunk-gears.gif', // Rotating gears
                'type' => 'custom_banner',
                'theme_value' => 'steampunk-gears'
            ],
            [
                'id' => 87,
                'name' => 'Lunar Eclipse',
                'price' => 350.00,
                'iconUrl' => 'https://media.tenor.com/0z5z5z5z5zIAAAAC/lunar-eclipse.gif', // Moon phases
                'type' => 'custom_banner',
                'theme_value' => 'lunar-eclipse'
            ],
            [
                'id' => 88,
                'name' => 'Glitch Matrix',
                'price' => 300.00,
                'iconUrl' => 'https://media.tenor.com/1z5z5z5z5zIAAAAC/glitch-matrix.gif', // Glitch effect
                'type' => 'custom_banner',
                'theme_value' => 'glitch-matrix'
            ],
            [
                'id' => 89,
                'name' => 'Aurora Dance',
                'price' => 360.00,
                'iconUrl' => 'https://media.tenor.com/2z5z5z5z5zIAAAAC/aurora-borealis.gif', // Aurora lights
                'type' => 'custom_banner',
                'theme_value' => 'aurora-dance'
            ],
            [
                'id' => 90,
                'name' => 'Galactic Spin',
                'price' => 340.00,
                'iconUrl' => 'https://media.tenor.com/3z5z5z5z5zIAAAAC/galaxy-spin.gif', // Galaxy rotation
                'type' => 'custom_banner',
                'theme_value' => 'galactic-spin'
            ],
            [
                'id' => 91,
                'name' => 'Rainbow Flux',
                'price' => 420.00,
                'iconUrl' => 'https://media.tenor.com/4z5z5z5z5zIAAAAC/rainbow-flux.gif', // Rainbow waves
                'type' => 'custom_banner',
                'theme_value' => 'rainbow-flux'
            ],

            // Profile Badges (IDs 92-107)
            ['id' => 92, 'name' => 'Verified Badge', 'price' => 200.00, 'iconUrl' => 'https://api.iconify.design/lucide/check-circle.svg', 'type' => 'badge', 'theme_value' => null],
            ['id' => 93, 'name' => 'Founder Badge', 'price' => 300.00, 'iconUrl' => 'https://api.iconify.design/lucide/award.svg', 'type' => 'badge', 'theme_value' => null],
            ['id' => 94, 'name' => 'VIP Badge', 'price' => 250.00, 'iconUrl' => 'https://api.iconify.design/lucide/crown.svg', 'type' => 'badge', 'theme_value' => null],
            ['id' => 95, 'name' => 'Creator Badge', 'price' => 200.00, 'iconUrl' => 'https://api.iconify.design/lucide/pen-tool.svg', 'type' => 'badge', 'theme_value' => null],
            ['id' => 96, 'name' => 'Explorer Badge', 'price' => 200.00, 'iconUrl' => 'https://api.iconify.design/lucide/compass.svg', 'type' => 'badge', 'theme_value' => null],
            ['id' => 97, 'name' => 'Legend Badge', 'price' => 280.00, 'iconUrl' => 'https://api.iconify.design/lucide/trophy.svg', 'type' => 'badge', 'theme_value' => null],
            ['id' => 98, 'name' => 'Pioneer Badge', 'price' => 260.00, 'iconUrl' => 'https://api.iconify.design/lucide/flag.svg', 'type' => 'badge', 'theme_value' => null],
            ['id' => 99, 'name' => 'Guardian Badge', 'price' => 240.00, 'iconUrl' => 'https://api.iconify.design/lucide/shield.svg', 'type' => 'badge', 'theme_value' => null],
            ['id' => 100, 'name' => 'Warrior Badge', 'price' => 270.00, 'iconUrl' => 'https://api.iconify.design/lucide/sword.svg', 'type' => 'badge', 'theme_value' => null],
            ['id' => 101, 'name' => 'Sage Badge', 'price' => 230.00, 'iconUrl' => 'https://api.iconify.design/lucide/book-open.svg', 'type' => 'badge', 'theme_value' => null],
            ['id' => 102, 'name' => 'Star Gazer Badge', 'price' => 250.00, 'iconUrl' => 'https://api.iconify.design/lucide/stars.svg', 'type' => 'badge', 'theme_value' => null],
            ['id' => 103, 'name' => 'Trailblazer Badge', 'price' => 290.00, 'iconUrl' => 'https://api.iconify.design/lucide/footprints.svg', 'type' => 'badge', 'theme_value' => null],
            ['id' => 104, 'name' => 'Elementalist Badge', 'price' => 260.00, 'iconUrl' => 'https://api.iconify.design/lucide/droplet.svg', 'type' => 'badge', 'theme_value' => null],
            ['id' => 105, 'name' => 'Innovator Badge', 'price' => 280.00, 'iconUrl' => 'https://api.iconify.design/lucide/lightbulb.svg', 'type' => 'badge', 'theme_value' => null],
            ['id' => 106, 'name' => 'Nomad Badge', 'price' => 240.00, 'iconUrl' => 'https://api.iconify.design/lucide/map.svg', 'type' => 'badge', 'theme_value' => null],
            ['id' => 107, 'name' => 'Champion Badge', 'price' => 300.00, 'iconUrl' => 'https://api.iconify.design/lucide/medal.svg', 'type' => 'badge', 'theme_value' => null],
        ];

        DB::table('items')->insert($items);
    }
}
