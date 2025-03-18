<?php

return [
    'driver' => env('IMAGE_DRIVER', 'gd'),
    'generator' => \Laravolt\Avatar\Generator\DefaultGenerator::class,
    'ascii' => false,
    'shape' => 'square',
    'width' => 100,
    'height' => 100,
    'responsive' => false,
    'chars' => 2,
    'fontSize' => 48,
    'uppercase' => true,
    'rtl' => false,
    'fonts' => [__DIR__ . '/../fonts/OpenSans-Bold.ttf'],
    'foregrounds' => ['#000000'],
    'backgrounds' => ['#FFDE21'],
    'border' => [
        'size' => 0,
        'color' => 'background',
        'radius' => 0,
    ],
    'theme' => ['custom'],
    'themes' => [
        'custom' => [
            'backgrounds' => ['#FFDE21'],
            'foregrounds' => ['#000000'],
            'fonts' => [__DIR__ . '/../fonts/OpenSans-Bold.ttf'],
            'fontSize' => 48,
            'shape' => 'square',
            'chars' => 2,
            'uppercase' => true,
        ],
    ],
];
