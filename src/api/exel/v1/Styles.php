<?php

namespace exel\v1;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Styles
{
    public static array $userNameCell = [
        'font' => [
            'size' => 12,
            'color' => [
                'rgb' => '434343',
            ],
            'bold'=>true,
        ],
        'fill' => [
            "fillType"=>Fill::FILL_SOLID,
            'color' => [
                'rgb' => 'FBBC04',
            ],
        ],
    ];

    public static array $headerOneCell = [
        'font' => [
            'size' => 12,
            'color' => [
                'rgb' => 'ffffff',
            ],
            'bold'=>true,
        ],
        'fill' => [
            "fillType"=>Fill::FILL_SOLID,
            'color' => [
                'rgb' => '000000',
            ],
        ],
        'alignment'=>[
            'horizontal'=>Alignment::HORIZONTAL_CENTER
        ]
    ];

    public static array $headerTwoCell = [
        'font' => [
            'size' => 12,
            'color' => [
                'rgb' => 'ffffff',
            ],
            'bold'=>true,
        ],
        'fill' => [
            "fillType"=>Fill::FILL_SOLID,
            'color' => [
                'rgb' => '351C75',
            ],
        ],
        'alignment'=>[
            'horizontal'=>Alignment::HORIZONTAL_CENTER
        ]
    ];

    public static array $headerThreeCell = [
        'font' => [
            'size' => 12,
            'color' => [
                'rgb' => 'ffffff',
            ],
            'bold'=>true,
        ],
        'fill' => [
            "fillType"=>Fill::FILL_SOLID,
            'color' => [
                'rgb' => '0B5394',
            ],
        ],
        'alignment'=>[
            'horizontal'=>Alignment::HORIZONTAL_CENTER
        ]
    ];

    public static array $headerFourCell = [
        'font' => [
            'size' => 12,
            'color' => [
                'rgb' => 'ffffff',
            ],
            'bold'=>true,
        ],
        'fill' => [
            "fillType"=>Fill::FILL_SOLID,
            'color' => [
                'rgb' => 'BF9000',
            ],
        ],
        'alignment'=>[
            'horizontal'=>Alignment::HORIZONTAL_CENTER
        ]
    ];
}