<?php

namespace LukaPeharda\TailwindCssColorPaletteGenerator;

class Color
{
    const HUE = 0;
    const SATURATION = 1;
    const LIGHTNESS = 2;

    /**
     * @var string
     */
    protected $hex;

    /**
     * @var array
     */
    protected $hsl;

    /**
     * Init object from hex string.
     *
     * If prefix "#" is a part of the given hex code it will be stripped.
     *
     * @param   string  $hex
     *
     * @return  self
     */
    public static function fromHex(string $hex): self
    {
        $color = new Color;

        $color->hex = str_replace('#', '', $hex);

        return $color;
    }

    /**
     * Init object from HSL values.
     *
     * HSL values can either be a decimal (0.5) number or percentage based (50).
     *
     * @param   float  $hue
     * @param   float  $saturation
     * @param   float  $lightness
     *
     * @return  self
     */
    public static function fromHsl(float $hue, float $saturation, float $lightness): self
    {
        $color = new Color;

        // Check if given HSL values are percentage based
        if ($hue > 1 || $saturation > 1 || $lightness > 1) {
            $color->hsl = [
                self::HUE => $hue / 100,
                self::SATURATION => $saturation / 100,
                self::LIGHTNESS => $lightness / 100,
            ];
        } else {
            $color->hsl = [
                self::HUE => $hue,
                self::SATURATION => $saturation,
                self::LIGHTNESS => $lightness,
            ];
        }

        return $color;
    }

    /**
     * Init object from RGB values.
     *
     * @param   int   $red
     * @param   int   $green
     * @param   int   $blue
     *
     * @return  self
     */
    public static function fromRgb(int $red, int $green, int $blue): self
    {
        $color = new Color;

        $color->hex = $color->rgbComponentToHex($red) . $color->rgbComponentToHex($green) . $color->rgbComponentToHex($blue);

        return $color;
    }

    /**
     * Return color value as hex string.
     *
     * Does not have "#" prepended.
     *
     * @return  string
     */
    public function getHex(): string
    {
        if ($this->hex === null) {
            $this->hex = $this->hslToHex($this->hsl);
        }

        return $this->hex;
    }

    /**
     * Return color value as HSL array.
     *
     * @return  array
     */
    public function getHsl(): array
    {
        if ($this->hsl === null) {
            $this->hsl = $this->hexToHsl($this->hex);
        }

        return $this->hsl;
    }

    /**
     * Return color as RGB array.
     *
     * @return  array
     */
    public function getRgb(): array
    {
        return array_map("hexdec", str_split($this->getHex(), 2));
    }

    /**
     * Convert hex string to HSL.
     *
     * @param   string  $hex
     *
     * @return  array
     */
    protected function hexToHsl(string $hex): array
    {
        if (strpos($hex, '#') === 0) {
            $hex = [$hex[1].$hex[2], $hex[3].$hex[4], $hex[5].$hex[6]];
        } else {
            $hex = [$hex[0].$hex[1], $hex[2].$hex[3], $hex[4].$hex[5]];
        }

        $rgb = array_map(function($part) {
            return hexdec($part) / 255;
        }, $hex);

        $max = max($rgb);
        $min = min($rgb);

        $l = ($max + $min) / 2;

        if ($max == $min) {
            $h = $s = 0;
        } else {
            $diff = $max - $min;
            $s = $l > 0.5 ? $diff / (2 - $max - $min) : $diff / ($max + $min);

            switch($max) {
                case $rgb[0]:
                    $h = ($rgb[1] - $rgb[2]) / $diff + ($rgb[1] < $rgb[2] ? 6 : 0);
                    break;
                case $rgb[1]:
                    $h = ($rgb[2] - $rgb[0]) / $diff + 2;
                    break;
                case $rgb[2]:
                    $h = ($rgb[0] - $rgb[1]) / $diff + 4;
                    break;
            }

            $h /= 6;
        }

        return [$h, $s, $l];
    }

    /**
     * Convert HSL array to hex.
     *
     * @param   array   $hsl
     *
     * @return  string
     */
    protected function hslToHex(array $hsl): string
    {
        list($h, $s, $l) = $hsl;

        if ($s == 0) {
            $s = 0.000001;
        }

        if ($s == 0) {
            $r = $g = $b = 1;
        } else {
            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;

            $r = $this->hueToRgb($p, $q, $h + 1/3);
            $g = $this->hueToRgb($p, $q, $h);
            $b = $this->hueToRgb($p, $q, $h - 1/3);
        }

        return $this->rgbComponentToHex($r) . $this->rgbComponentToHex($g) . $this->rgbComponentToHex($b);
    }

    /**
     * Convert hue values to RGB.
     *
     * @param   float  $p
     * @param   float  $q
     * @param   float  $t
     *
     * @return  float
     */
    protected function hueToRgb(float $p, float $q, float $t): float
    {
        if ($t < 0) $t += 1;
        if ($t > 1) $t -= 1;
        if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
        if ($t < 1/2) return $q;
        if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;

        return $p;
    }

    /**
     * Convert RGB's value to hex.
     *
     * @param   float     $rgb
     *
     * @return  string
     */
    protected function rgbComponentToHex(float $rgb): string
    {
        return str_pad(dechex((int) ($rgb * 255)), 2, '0', STR_PAD_LEFT);
    }

    /**
     * Output color's hex code.
     *
     * @return  string
     */
    public function __toString(): string
    {
        return $this->getHex();
    }
}
