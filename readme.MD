# TailwindCSS Color Palette Generator

Generates TailwindCSS color palette (ranging from `50` to `900`) from a single base color (which can be given as a hex value, HSL or RGB).

Uses HSL color model and base color lightness to generate step colors by raising (or lowering) lightness in steps (and considering bound thresholds).

## Installation

Install it via composer by running:

`composer require lukapeharda/tailwindcss-color-palette-generator`

## Usage

First, import needed namespaces and classes:

```php
use LukaPeharda\TailwindCssColorPaletteGenerator\Color;
use LukaPeharda\TailwindCssColorPaletteGenerator\PaletteGenerator;
```

Then create your base color:

```php
// from hex
$baseColor = Color::fromHex('#ffff00');

// or from RGB
$baseColor = Color::fromRgb(255, 255, 0);

// or from HSL
$baseColor = Color::fromHsl(60, 100, 50); // or $baseColor = Color::fromHsl(0.6, 1, 0.5);
```

Lastly, use base color to create a color palette:

```php
$paletteGenerator = new PaletteGenerator;
$paletteGenerator->setBaseColor($baseColor);
$palette = $paletteGenerator->getPalette();
```

Generated `$palette` will be an array where keys are TailwindCSS color steps and values `Color` objects:

```php
$palette = [
    50 => Color,
    100 => Color,
    200 => Color,
    300 => Color,
    ...
];
```

You can then loop over it to generate CSS variables or use it anyway you see fit:

```php
foreach ($palette as $key => $color) {
    echo '--color-primary-' . $key . ': #' . $color->getHex() . ';';
}
```

Extend color settings in your `tailwind.config.js` file and add `primary` color pallete:

```js
module.exports = {
    theme: {
        extend: {
            colors: {
                primary: {
                    50: 'var(--color-primary-50, #F5F3FF)',
                    100: 'var(--color-primary-100, #EDE9FE)',
                    200: 'var(--color-primary-200, #DDD6FE)',
                    300: 'var(--color-primary-300, #C4B5FD)',
                    400: 'var(--color-primary-400, #A78BFA)',
                    500: 'var(--color-primary-500, #8B5CF6)',
                    600: 'var(--color-primary-600, #7C3AED)',
                    700: 'var(--color-primary-700, #6D28D9)',
                    800: 'var(--color-primary-800, #5B21B6)',
                    900: 'var(--color-primary-900, #4C1D95)',
                }
            }
        }
    }
}
```

Afterwards you can use your color as regular CSS Tailwind class, for example as `text-primary-100` or `bg-primary-300`.

## Defaults

`PaletteGenerator` class has some configurable options set to a sensible defaults.

### Base color step value

By default base color step value is `500`. This means that 5 lighter colors and 4 darker will be generated in a palette.

You can change it by calling `setBaseValue` method on the `PaletteGenerator` object:

```php
$paletteGenerator->setBaseValue(400);
```

By setting the base value to `400` 4 lighter colors and 5 darker will be generated.

### Bounds thresholds

By default the lightest generated color will have a lightness value of 90% and the darkest will have the lightness value of 10%.

You can change this by calling `setThresholdLightest` and `setThresholdDarkest` methods on `PaletteGenerator` object:

```php
$paletteGenerator->setThresholdLightest(80); // or $paletteGenerator->setThresholdLightest(0.8);
$paletteGenerator->setThresholdDarkest(20); // or $paletteGenerator->setThresholdDarkest(0.2);
```

### Color steps

By default, TailwindCSS 2.x color steps range is used (from `50` to `900`).

You can override it by calling `setColorSteps` method on `PaletteGenerator` object by giving it an array with step values:

```php
$paletteGenerator->setColorSteps([100, 200, 300, 400, 500, 600, 700]);
```

## Todos

Different strategies for generating colors are planned to be developed.
