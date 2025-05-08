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
            // Subscriptions (IDs 1-3)
            ['id' => 1, 'name' => 'Basic', 'price' => 0.00, 'iconUrl' => 'https://api.iconify.design/mdi/seed-outline.svg', 'type' => 'subscription', 'theme_value' => null, 'amount' => null],
            ['id' => 2, 'name' => 'Premium', 'price' => 9.99, 'iconUrl' => 'https://api.iconify.design/mdi/shield-star-outline.svg', 'type' => 'subscription', 'theme_value' => null, 'amount' => null],
            ['id' => 3, 'name' => 'Elite', 'price' => 19.99, 'iconUrl' => 'https://api.iconify.design/mdi/crown-circle-outline.svg', 'type' => 'subscription', 'theme_value' => null, 'amount' => null],

            // Credit Packs (IDs 4-11)
            ['id' => 4, 'name' => '100 Credits', 'price' => 1.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack', 'theme_value' => null, 'amount' => 100],
            ['id' => 5, 'name' => '200 Credits', 'price' => 3.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack', 'theme_value' => null, 'amount' => 200],
            ['id' => 6, 'name' => '500 Credits', 'price' => 7.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack', 'theme_value' => null, 'amount' => 500],
            ['id' => 7, 'name' => '750 Credits', 'price' => 11.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack', 'theme_value' => null, 'amount' => 750],
            ['id' => 8, 'name' => '1000 Credits', 'price' => 14.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack', 'theme_value' => null, 'amount' => 1000],
            ['id' => 9, 'name' => '1500 Credits', 'price' => 24.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack', 'theme_value' => null, 'amount' => 1500],
            ['id' => 10, 'name' => '2500 Credits', 'price' => 34.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack', 'theme_value' => null, 'amount' => 2500],
            ['id' => 11, 'name' => '5000 Credits', 'price' => 59.99, 'iconUrl' => 'https://api.iconify.design/lucide/coins.svg', 'type' => 'credit_pack', 'theme_value' => null, 'amount' => 5000],

            // Profile Icons (IDs 12-27)
            ['id' => 12, 'name' => 'Mini Crown', 'price' => 150.00, 'iconUrl' => 'https://api.iconify.design/lucide/crown.svg', 'type' => 'profile_icon', 'theme_value' => null, 'amount' => null],
            ['id' => 13, 'name' => 'Shining Star', 'price' => 100.00, 'iconUrl' => 'https://api.iconify.design/lucide/star.svg', 'type' => 'profile_icon', 'theme_value' => null, 'amount' => null],
            ['id' => 14, 'name' => 'Glowing Heart', 'price' => 100.00, 'iconUrl' => 'https://api.iconify.design/lucide/heart.svg', 'type' => 'profile_icon', 'theme_value' => null, 'amount' => null],
            ['id' => 15, 'name' => 'Ghostly Aura', 'price' => 200.00, 'iconUrl' => 'https://api.iconify.design/lucide/ghost.svg', 'type' => 'profile_icon', 'theme_value' => null, 'amount' => null],
            ['id' => 16, 'name' => 'Crystal Gem', 'price' => 250.00, 'iconUrl' => 'https://api.iconify.design/lucide/diamond.svg', 'type' => 'profile_icon', 'theme_value' => null, 'amount' => null],
            ['id' => 17, 'name' => 'Thunder Bolt', 'price' => 150.00, 'iconUrl' => 'https://api.iconify.design/lucide/zap.svg', 'type' => 'profile_icon', 'theme_value' => null, 'amount' => null],
            ['id' => 18, 'name' => 'Moon Glow', 'price' => 120.00, 'iconUrl' => 'https://api.iconify.design/lucide/moon.svg', 'type' => 'profile_icon', 'theme_value' => null, 'amount' => null],
            ['id' => 19, 'name' => 'Sun Flare', 'price' => 130.00, 'iconUrl' => 'https://api.iconify.design/lucide/sun.svg', 'type' => 'profile_icon', 'theme_value' => null, 'amount' => null],
            ['id' => 20, 'name' => 'Flame Crest', 'price' => 140.00, 'iconUrl' => 'https://api.iconify.design/lucide/flame.svg', 'type' => 'profile_icon', 'theme_value' => null, 'amount' => null],
            ['id' => 21, 'name' => 'Snowflake Spark', 'price' => 110.00, 'iconUrl' => 'https://api.iconify.design/lucide/snowflake.svg', 'type' => 'profile_icon', 'theme_value' => null, 'amount' => null],
            ['id' => 22, 'name' => 'Leaf Whisper', 'price' => 120.00, 'iconUrl' => 'https://api.iconify.design/lucide/leaf.svg', 'type' => 'profile_icon', 'theme_value' => null, 'amount' => null],
            ['id' => 23, 'name' => 'Wave Ripple', 'price' => 130.00, 'iconUrl' => 'https://api.iconify.design/lucide/waves.svg', 'type' => 'profile_icon', 'theme_value' => null, 'amount' => null],
            ['id' => 24, 'name' => 'Cloud Drift', 'price' => 150.00, 'iconUrl' => 'https://api.iconify.design/lucide/cloud.svg', 'type' => 'profile_icon', 'theme_value' => null, 'amount' => null],
            ['id' => 25, 'name' => 'Gear Spin', 'price' => 160.00, 'iconUrl' => 'https://api.iconify.design/lucide/cog.svg', 'type' => 'profile_icon', 'theme_value' => null, 'amount' => null],
            ['id' => 26, 'name' => 'Anchor Drop', 'price' => 140.00, 'iconUrl' => 'https://api.iconify.design/lucide/anchor.svg', 'type' => 'profile_icon', 'theme_value' => null, 'amount' => null],
            ['id' => 27, 'name' => 'Feather Light', 'price' => 110.00, 'iconUrl' => 'https://api.iconify.design/lucide/feather.svg', 'type' => 'profile_icon', 'theme_value' => null, 'amount' => null],

            // Backgrounds (IDs 28-43)
            ['id' => 28, 'name' => 'Cosmic Wave', 'price' => 300.00, 'iconUrl' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExOWU2YzE4aTY4d3ozM29rdTdzbXdxOXd6cThuY2NoeHZmbnBlN3lvMSZlcD12MV9naWZzX3NlYXJjaCZjdD1n/Fbox1ygIqnga5dLinz/giphy.gif', 'type' => 'background', 'theme_value' => null, 'amount' => null],
            ['id' => 29, 'name' => 'Starry Night', 'price' => 400.00, 'iconUrl' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExaTNsYnFmdzF0aWs5Njg1NW9ocXBzc3p2ZGxrOWo0dHAza2Nnb3F5NyZlcD12MV9naWZzX3NlYXJjaCZjdD1n/g9wbFB61YEh1u/giphy.gif', 'type' => 'background', 'theme_value' => null, 'amount' => null],
            ['id' => 30, 'name' => 'Minimal Waves', 'price' => 350.00, 'iconUrl' => 'https://media3.giphy.com/media/v1.Y2lkPTc5MGI3NjExdTFheDd1azVobmc5c3VnMjY1NGtid2tqbmdvbWs5bmhmOTZrYmJwNyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/nFq0KlrRGcKhiUryz9/giphy.gif', 'type' => 'background', 'theme_value' => null, 'amount' => null],
            ['id' => 31, 'name' => 'Pastel Sky', 'price' => 350.00, 'iconUrl' => 'https://media.giphy.com/media/3ov9jXzM2LcII0KvLi/giphy.gif?cid=ecf05e47wh1gy8r0ta9ba35uwnpy8p5ntlpj1k3vngafqprb&ep=v1_gifs_search&rid=giphy.gif&ct=g', 'type' => 'background', 'theme_value' => null, 'amount' => null],
            ['id' => 32, 'name' => 'Urban Glow', 'price' => 400.00, 'iconUrl' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExNGNsb3RrenZzcHh1aGc5bzJrY2szNzBlZ3Ftemtkbjk3YmFpYTVhYiZlcD12MV9naWZzX3NlYXJjaCZjdD1n/kK7qOFLoDeQGpIISJ1/giphy.gif', 'type' => 'background', 'theme_value' => null, 'amount' => null],
            ['id' => 33, 'name' => 'Forest Mist', 'price' => 300.00, 'iconUrl' => 'https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExNGtlMHlxZmw3a2ZzYnptMTBteWxncHp0cHo1ZDlvcno1a243M3hreSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/KWcGnX2iz0G1fMMHrO/giphy.gif', 'type' => 'background', 'theme_value' => null, 'amount' => null],
            ['id' => 34, 'name' => 'Ocean Depth', 'price' => 350.00, 'iconUrl' => 'https://media.giphy.com/media/l41JJTQxEIBHAQXU4/giphy.gif?cid=ecf05e47o2rt4fvdymxwm9p52cg42atdq6ktlyc05mrzx3uj&ep=v1_gifs_related&rid=giphy.gif&ct=g', 'type' => 'background', 'theme_value' => null, 'amount' => null],
            ['id' => 35, 'name' => 'Desert Dunes', 'price' => 320.00, 'iconUrl' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExbGVseXRrMXhweGYwNzAwOGx1dzJ6NWtxbmZxd3A2OGo0eWdxdGUwZCZlcD12MV9naWZzX3NlYXJjaCZjdD1n/VYopUOf4Wjd28/giphy.gif', 'type' => 'background', 'theme_value' => null, 'amount' => null],
            ['id' => 36, 'name' => 'Mountain Peak', 'price' => 380.00, 'iconUrl' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExa2F0ZXE4ZTI4YXJ6ZnhxZ2p0YnQ3MjM3MHVieXlrbWRjazRsejljYiZlcD12MV9naWZzX3NlYXJjaCZjdD1n/GBDYmRFFwyNo8OHWfs/giphy.gif', 'type' => 'background', 'theme_value' => null, 'amount' => null],
            ['id' => 37, 'name' => 'Polar Glow', 'price' => 450.00, 'iconUrl' => 'https://media.giphy.com/media/3o7at03qftvw3vYnuM/giphy.gif?cid=ecf05e47m9tevshd3acigewkb72n9olrewkzy6a5dya8uq02&ep=v1_gifs_related&rid=giphy.gif&ct=g', 'type' => 'background', 'theme_value' => null, 'amount' => null],
            ['id' => 38, 'name' => 'Minecraft Nether', 'price' => 340.00, 'iconUrl' => 'https://media.giphy.com/media/kbb0RKJpfNwGxgxYMn/giphy.gif?cid=ecf05e47hn120p0zuvr528tfwcaw7nifeqof8zospo8pzbj1&ep=v1_gifs_search&rid=giphy.gif&ct=g', 'type' => 'background', 'theme_value' => null, 'amount' => null],
            ['id' => 39, 'name' => 'Dusk Metropolis', 'price' => 410.00, 'iconUrl' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExbHBhMHJvcmNrc3dxNnllb3Nsc3cxZDQ0OWtueHFxaGF6aXl3OHl6OCZlcD12MV9naWZzX3NlYXJjaCZjdD1n/ilDP0ex8AWpqTT1Kz0/giphy.gif', 'type' => 'background', 'theme_value' => null, 'amount' => null],
            ['id' => 40, 'name' => 'Golden Fields', 'price' => 330.00, 'iconUrl' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExYjk4ODJqN2JyZGw3Y2MzdjVoZDJlcjN3eDE1cXZkcmhta2twaXF6MCZlcD12MV9naWZzX3NlYXJjaCZjdD1n/KShuE9tlWzJ3QLMhvu/giphy.gif', 'type' => 'background', 'theme_value' => null, 'amount' => null],
            ['id' => 41, 'name' => 'Volcanic Ash', 'price' => 390.00, 'iconUrl' => 'https://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExem5xZXp5YXEydzlrNjJyejVtdXpubG1mcDNvNGI5bzNrY3VnOHlkYiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/9E7kUhnT9eDok/giphy.gif', 'type' => 'background', 'theme_value' => null, 'amount' => null],
            ['id' => 42, 'name' => 'Nebula Cloud', 'price' => 420.00, 'iconUrl' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExdHIwdXRpZG1mZ21kOWZ3bHFjZzlwMmZjaXkxbGZwcGVyYTl1ODkxaCZlcD12MV9naWZzX3NlYXJjaCZjdD1n/Q7uDR6NALqbZp7166y/giphy.gif', 'type' => 'background', 'theme_value' => null, 'amount' => null],
            ['id' => 43, 'name' => 'Twilight Horizon', 'price' => 370.00, 'iconUrl' => 'https://media.giphy.com/media/VBd0clQeeR1dNYFKIn/giphy.gif?cid=ecf05e47odtbnjiu8frbo1nottbxtx6mtd7ec798to3bzfrb&ep=v1_gifs_search&rid=giphy.gif&ct=g', 'type' => 'background', 'theme_value' => null, 'amount' => null],

            // Profile Fonts (IDs 44-59)
            ['id' => 44, 'name' => 'Pixel Art', 'price' => 500.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null, 'amount' => null],
            ['id' => 45, 'name' => 'Comic Sans', 'price' => 550.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null, 'amount' => null],
            ['id' => 46, 'name' => 'Gothic', 'price' => 500.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null, 'amount' => null],
            ['id' => 47, 'name' => 'Cursive', 'price' => 600.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null, 'amount' => null],
            ['id' => 48, 'name' => 'Typewriter', 'price' => 550.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null, 'amount' => null],
            ['id' => 49, 'name' => 'Bubble', 'price' => 520.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null, 'amount' => null],
            ['id' => 50, 'name' => 'Neon', 'price' => 530.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null, 'amount' => null],
            ['id' => 51, 'name' => 'Graffiti', 'price' => 580.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null, 'amount' => null],
            ['id' => 52, 'name' => 'Retro', 'price' => 590.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null, 'amount' => null],
            ['id' => 53, 'name' => 'Cyberpunk', 'price' => 560.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null, 'amount' => null],
            ['id' => 54, 'name' => 'Western', 'price' => 540.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null, 'amount' => null],
            ['id' => 55, 'name' => 'Chalkboard', 'price' => 510.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null, 'amount' => null],
            ['id' => 56, 'name' => 'Horror', 'price' => 570.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null, 'amount' => null],
            ['id' => 57, 'name' => 'Futuristic', 'price' => 520.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null, 'amount' => null],
            ['id' => 58, 'name' => 'Handwritten', 'price' => 600.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null, 'amount' => null],
            ['id' => 59, 'name' => 'Bold Script', 'price' => 530.00, 'iconUrl' => 'https://api.iconify.design/lucide/type.svg', 'type' => 'profile_font', 'theme_value' => null, 'amount' => null],

            // Name Effects (IDs 60-75)
            ['id' => 60, 'name' => 'Gradient Fade', 'price' => 350.00, 'iconUrl' => 'https://api.iconify.design/lucide/blend.svg', 'type' => 'name_effect', 'theme_value' => null, 'amount' => null],
            ['id' => 61, 'name' => 'Golden Outline', 'price' => 400.00, 'iconUrl' => 'https://api.iconify.design/lucide/badge.svg', 'type' => 'name_effect', 'theme_value' => null, 'amount' => null],
            ['id' => 62, 'name' => 'Dark Pulse', 'price' => 300.00, 'iconUrl' => 'https://api.iconify.design/lucide/vibrate.svg', 'type' => 'name_effect', 'theme_value' => null, 'amount' => null],
            ['id' => 63, 'name' => 'Cosmic Shine', 'price' => 450.00, 'iconUrl' => 'https://api.iconify.design/lucide/stars.svg', 'type' => 'name_effect', 'theme_value' => null, 'amount' => null],
            ['id' => 64, 'name' => 'Neon Edge', 'price' => 320.00, 'iconUrl' => 'https://api.iconify.design/lucide/lightbulb.svg', 'type' => 'name_effect', 'theme_value' => null, 'amount' => null],
            ['id' => 65, 'name' => 'Frost Glow', 'price' => 340.00, 'iconUrl' => 'https://api.iconify.design/lucide/snowflake.svg', 'type' => 'name_effect', 'theme_value' => null, 'amount' => null],
            ['id' => 66, 'name' => 'Fire Flicker', 'price' => 360.00, 'iconUrl' => 'https://api.iconify.design/lucide/flame.svg', 'type' => 'name_effect', 'theme_value' => null, 'amount' => null],
            ['id' => 67, 'name' => 'Emerald Sheen', 'price' => 370.00, 'iconUrl' => 'https://api.iconify.design/lucide/gem.svg', 'type' => 'name_effect', 'theme_value' => null, 'amount' => null],
            ['id' => 68, 'name' => 'Phantom Haze', 'price' => 310.00, 'iconUrl' => 'https://api.iconify.design/lucide/cloud-fog.svg', 'type' => 'name_effect', 'theme_value' => null, 'amount' => null],
            ['id' => 69, 'name' => 'Electric Glow', 'price' => 380.00, 'iconUrl' => 'https://api.iconify.design/lucide/zap.svg', 'type' => 'name_effect', 'theme_value' => null, 'amount' => null],
            ['id' => 70, 'name' => 'Solar Flare', 'price' => 390.00, 'iconUrl' => 'https://api.iconify.design/lucide/sun.svg', 'type' => 'name_effect', 'theme_value' => null, 'amount' => null],
            ['id' => 71, 'name' => 'Wave Shimmer', 'price' => 340.00, 'iconUrl' => 'https://api.iconify.design/lucide/waves.svg', 'type' => 'name_effect', 'theme_value' => null, 'amount' => null],
            ['id' => 72, 'name' => 'Crystal Pulse', 'price' => 400.00, 'iconUrl' => 'https://api.iconify.design/lucide/diamond.svg', 'type' => 'name_effect', 'theme_value' => null, 'amount' => null],
            ['id' => 73, 'name' => 'Mystic Aura', 'price' => 360.00, 'iconUrl' => 'https://api.iconify.design/lucide/sparkles.svg', 'type' => 'name_effect', 'theme_value' => null, 'amount' => null],
            ['id' => 74, 'name' => 'Shadow Veil', 'price' => 330.00, 'iconUrl' => 'https://api.iconify.design/lucide/shield.svg', 'type' => 'name_effect', 'theme_value' => null, 'amount' => null],
            ['id' => 75, 'name' => 'Digital Pulse', 'price' => 380.00, 'iconUrl' => 'https://api.iconify.design/mdi/pulse.svg?color=%2300FFFF', 'type' => 'name_effect', 'theme_value' => null, 'amount' => null],

            // Custom Banners (IDs 76-91)
            ['id' => 76, 'name' => 'Misty Peaks', 'price' => 300.00, 'iconUrl' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExemgxbXlwbmZ3NjN5MGRmd3h0c3l6dnRiOHRnaW1lZWdqdDFrajFuYiZlcD12MV9naWZzX3NlYXJjaCZjdD1n/Lh1o7roURqakFBq9sF/giphy.gif', 'type' => 'custom_banner', 'theme_value' => 'mountain-mist', 'amount' => null],
            ['id' => 77, 'name' => 'Cascading Falls', 'price' => 350.00, 'iconUrl' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExNndqanNtaHdudHV2YnRydGVyYXFra3B1aDg3Z3o1YjB6MXFpdnIxNiZlcD12MV9naWZzX3NlYXJjaCZjdD1n/d3fRdpzqSi8ilFPa/giphy.gif', 'type' => 'custom_banner', 'theme_value' => 'forest-waterfall', 'amount' => null],
            ['id' => 78, 'name' => 'Stormy Waves', 'price' => 320.00, 'iconUrl' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExbnVtbHcyNWN6am1pdzdrZ2t5cTlmeXZidnU3OWRnYXJ0c2Ewcjd0byZlcD12MV9naWZzX3NlYXJjaCZjdD1n/xUOxeUqZbV4vSPBXt6/giphy.gif', 'type' => 'custom_banner', 'theme_value' => 'ocean-storm', 'amount' => null],
            ['id' => 79, 'name' => 'Desert Sunset', 'price' => 340.00, 'iconUrl' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExcnJ0bzVxcjZma3k0bmF2ejZva3h0ZmlmdHVyN24xNWw1bmFtam5oNyZlcD12MV9naWZzX3NlYXJjaCZjdD1n/s6vX4peRYz9taH3az7/giphy.gif', 'type' => 'custom_banner', 'theme_value' => 'desert-horizon', 'amount' => null],
            ['id' => 80, 'name' => 'Northern Lights', 'price' => 400.00, 'iconUrl' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExczg4eDYwNHd0MDFjMGQ0czVicmVuc3NibmthN2d3aWJsbW52eDRsbCZlcD12MV9naWZzX3NlYXJjaCZjdD1n/35NGT2lm6mfHlNAkMu/giphy.gif', 'type' => 'custom_banner', 'theme_value' => 'aurora-sky', 'amount' => null],
            ['id' => 81, 'name' => 'Gentle Waterfall', 'price' => 330.00, 'iconUrl' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExYnoxYjl2cDdnY204emExZ2pmc3QxbXdmcnNuc2dqa2JwZmhqZjd1MSZlcD12MV9naWZzX3NlYXJjaCZjdD1n/hVBuMyiTAn0yZ5nzGp/giphy.gif', 'type' => 'custom_banner', 'theme_value' => 'waterfall-cascade', 'amount' => null],
            ['id' => 82, 'name' => 'Autumn Drift', 'price' => 360.00, 'iconUrl' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExNnB5dHduYjV4NmFubHNxM2UyYjZ2YjlvYXZuaHIxY3AwenR0bnY2MSZlcD12MV9naWZzX3NlYXJjaCZjdD1n/ToMjGpQe9iCuexMtEg8/giphy.gif', 'type' => 'custom_banner', 'theme_value' => 'falling-leaves', 'amount' => null],
            ['id' => 83, 'name' => 'Tech Grid', 'price' => 370.00, 'iconUrl' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExZTdjYTJ5aHYzd3hqejZqZmJ0cHF1OWh4aGF6NGVodjFqdGFzZXBpMyZlcD12MV9naWZzX3NlYXJjaCZjdD1n/3ornkfqNPnwvTcPU4w/giphy.gif', 'type' => 'custom_banner', 'theme_value' => 'neon-grid', 'amount' => null],
            ['id' => 84, 'name' => 'Particle Flow', 'price' => 380.00, 'iconUrl' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExeDZveG1qYTQ0bWw2dHp5Mjk3eGU2ZDd0azd2YWl2cmFncHF4M2V2NSZlcD12MV9naWZzX3NlYXJjaCZjdD1n/iMBEwpRodEBlpuTyVD/giphy.gif', 'type' => 'custom_banner', 'theme_value' => 'digital-flow', 'amount' => null],
            ['id' => 85, 'name' => 'Circuit Pulse', 'price' => 310.00, 'iconUrl' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExd3NrdTF0Z3hhZGU4YnhpZXJkaWEzMWo2Nmw5dTkwMnp5bjlzZXM4aiZlcD12MV9naWZzX3NlYXJjaCZjdD1n/AdtB8TtizElk0OrRGR/giphy.gif', 'type' => 'custom_banner', 'theme_value' => 'tech-circuit', 'amount' => null],
            ['id' => 86, 'name' => 'Matrix Rain', 'price' => 390.00, 'iconUrl' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExeWRrem9oejRiYWRzdWU2aG1lbGxpbXRybzN0cGo2YXRudjZ0aTlrNyZlcD12MV9naWZzX3NlYXJjaCZjdD1n/gCZ9N4IMDPaoM/giphy.gif', 'type' => 'custom_banner', 'theme_value' => 'matrix-code', 'amount' => null],
            ['id' => 87, 'name' => 'Cyber Skyline', 'price' => 350.00, 'iconUrl' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExdnh2c3Z1ZmhhdTEyc3VibjRndTl4MmZ4aThyNDYxMTRiaHE3M256NyZlcD12MV9naWZzX3NlYXJjaCZjdD1n/x45I8CY1Vs45G/giphy.gif', 'type' => 'custom_banner', 'theme_value' => 'cyber-city', 'amount' => null],
            ['id' => 88, 'name' => 'Code Rainfall', 'price' => 300.00, 'iconUrl' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExa3JjOWZ5cXh4OW4zbzN5dHh3ZnkyOHhjNGdqaTY0cmN0Z2o5dTkwYiZlcD12MV9naWZzX3NlYXJjaCZjdD1n/26BRGoqbUQvk8nwTC/giphy.gif', 'type' => 'custom_banner', 'theme_value' => 'code-scroll', 'amount' => null],
            ['id' => 89, 'name' => 'Holo Waves', 'price' => 360.00, 'iconUrl' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExNTV0cDY5dzlnc2t2ZHFmd2RpOTByZ28wcGF1bWhxZzBwajdmOWYxZSZlcD12MV9naWZzX3NlYXJjaCZjdD1n/qAys67eteyUPTirtN5/giphy.gif', 'type' => 'custom_banner', 'theme_value' => 'holo-scan', 'amount' => null],
            ['id' => 90, 'name' => 'Neon Pulse', 'price' => 340.00, 'iconUrl' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExc2IybzMzY2YwamIyN2g1MnVpYXR3bWVjaDNyeTRwejA3YTB6cG1naCZlcD12MV9naWZzX3NlYXJjaCZjdD1n/5aUU1Zf7tp8w0fwcM9/giphy.gif', 'type' => 'custom_banner', 'theme_value' => 'neon-streets', 'amount' => null],
            ['id' => 91, 'name' => 'Star Warp', 'price' => 420.00, 'iconUrl' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExZHAzZzA3MmJiZHN6NzdwY3E5emNycThoNTZlOWNobm4yNm0wNzNiMSZlcD12MV9naWZzX3NlYXJjaCZjdD1n/3og0IFrHkIglEOg8Ba/giphy.gif', 'type' => 'custom_banner', 'theme_value' => 'space-warp', 'amount' => null],

            // Profile Badges (IDs 92-107)
            ['id' => 92, 'name' => 'Verified Badge', 'price' => 200.00, 'iconUrl' => 'https://api.iconify.design/lucide/check-circle.svg', 'type' => 'badge', 'theme_value' => null, 'amount' => null],
            ['id' => 93, 'name' => 'Founder Badge', 'price' => 300.00, 'iconUrl' => 'https://api.iconify.design/lucide/award.svg', 'type' => 'badge', 'theme_value' => null, 'amount' => null],
            ['id' => 94, 'name' => 'VIP Badge', 'price' => 250.00, 'iconUrl' => 'https://api.iconify.design/lucide/crown.svg', 'type' => 'badge', 'theme_value' => null, 'amount' => null],
            ['id' => 95, 'name' => 'Creator Badge', 'price' => 200.00, 'iconUrl' => 'https://api.iconify.design/lucide/pen-tool.svg', 'type' => 'badge', 'theme_value' => null, 'amount' => null],
            ['id' => 96, 'name' => 'Explorer Badge', 'price' => 200.00, 'iconUrl' => 'https://api.iconify.design/lucide/compass.svg', 'type' => 'badge', 'theme_value' => null, 'amount' => null],
            ['id' => 97, 'name' => 'Legend Badge', 'price' => 280.00, 'iconUrl' => 'https://api.iconify.design/lucide/trophy.svg', 'type' => 'badge', 'theme_value' => null, 'amount' => null],
            ['id' => 98, 'name' => 'Pioneer Badge', 'price' => 260.00, 'iconUrl' => 'https://api.iconify.design/lucide/flag.svg', 'type' => 'badge', 'theme_value' => null, 'amount' => null],
            ['id' => 99, 'name' => 'Guardian Badge', 'price' => 240.00, 'iconUrl' => 'https://api.iconify.design/lucide/shield.svg', 'type' => 'badge', 'theme_value' => null, 'amount' => null],
            ['id' => 100, 'name' => 'Warrior Badge', 'price' => 270.00, 'iconUrl' => 'https://api.iconify.design/lucide/sword.svg', 'type' => 'badge', 'theme_value' => null, 'amount' => null],
            ['id' => 101, 'name' => 'Sage Badge', 'price' => 230.00, 'iconUrl' => 'https://api.iconify.design/lucide/book-open.svg', 'type' => 'badge', 'theme_value' => null, 'amount' => null],
            ['id' => 102, 'name' => 'Star Gazer Badge', 'price' => 250.00, 'iconUrl' => 'https://api.iconify.design/lucide/stars.svg', 'type' => 'badge', 'theme_value' => null, 'amount' => null],
            ['id' => 103, 'name' => 'Trailblazer Badge', 'price' => 290.00, 'iconUrl' => 'https://api.iconify.design/lucide/footprints.svg', 'type' => 'badge', 'theme_value' => null, 'amount' => null],
            ['id' => 104, 'name' => 'Elementalist Badge', 'price' => 260.00, 'iconUrl' => 'https://api.iconify.design/lucide/droplet.svg', 'type' => 'badge', 'theme_value' => null, 'amount' => null],
            ['id' => 105, 'name' => 'Innovator Badge', 'price' => 280.00, 'iconUrl' => 'https://api.iconify.design/lucide/lightbulb.svg', 'type' => 'badge', 'theme_value' => null, 'amount' => null],
            ['id' => 106, 'name' => 'Nomad Badge', 'price' => 240.00, 'iconUrl' => 'https://api.iconify.design/lucide/map.svg', 'type' => 'badge', 'theme_value' => null, 'amount' => null],
            ['id' => 107, 'name' => 'Champion Badge', 'price' => 300.00, 'iconUrl' => 'https://api.iconify.design/lucide/medal.svg', 'type' => 'badge', 'theme_value' => null, 'amount' => null],
        ];

        DB::table('items')->insert($items);
    }
}
