<?php

namespace exel\v1;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Styles
{

    public static function generate(array $font=[], array $fill=[], array $alignment=[]):array{
        $fontDefault = [self::$font12];
        $fillDefault = [];
        $alignmentDefault = [self::$alignmentCenter];


        return [
            'font'=>array_merge(...$fontDefault, ...$font),
            'fill'=>array_merge(...$fillDefault, ...$fill),
            'alignment'=>array_merge(...$alignmentDefault, ...$alignment),
        ];
    }


    public static function userNameCell(): array{
        return self::generate([self::$bold], [self::$fillSolid,self::$gray]);
    }

    public static function headerOneCell(): array{
        return self::generate([self::$bold, self::$white], [self::$fillSolid,self::$black]);
    }

    public static function headerTwoCell(): array{
        return self::generate([self::$bold, self::$white], [self::$fillSolid,self::$purple]);
    }

    public static function headerThreeCell(): array{
        return self::generate([self::$bold, self::$white], [self::$fillSolid,self::$blue]);
    }

    public static function headerFourCell(): array{
        return self::generate([self::$bold, self::$white], [self::$fillSolid,self::$orange]);
    }


    public static array $white = [
        'color' => [
            'rgb' => 'ffffff',
        ],
    ];
    public static array $gray = [
        'color' => [
            'rgb' => 'f2f2f2',
        ],
    ];
    public static array $black = [
        'color' => [
            'rgb' => '000000',
        ],
    ];
    public static array $purple = [
        'color' => [
            'rgb' => '351C75',
        ],
    ];
    public static array $orange = [
        'color' => [
            'rgb' => 'BF9000',
        ],
    ];
    public static array $blue = [
        'color' => [
            'rgb' => '0B5394',
        ],
    ];
    public static array $bold = [
        'bold'=>true,
    ];
    public static array $font12 = [
        'size' => 12,
    ];
    public static array $alignmentCenter = [
        'horizontal'=>Alignment::HORIZONTAL_CENTER
    ];
    public static array $alignmentRight = [
        'horizontal'=>Alignment::HORIZONTAL_RIGHT
    ];
    public static array $fillSolid = [
        "fillType"=>Fill::FILL_SOLID,
    ];
}