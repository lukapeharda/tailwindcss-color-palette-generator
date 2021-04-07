# TailwindCSS Color Pallete Generator

Generates TailwindCSS color pallete (ranging from "50" to "900") from a single base color (which can be given as a hex value, HSL or RGB).

Uses HSL color model and base color lightness to generate step colors by raising (or lowering) lightness in steps (and considering bound thresholds).

## Installation

Install it via composer by running:

`composer require lukapeharda/tailwindcss-color-pallete-generator`

## Usage

First, import needed namespaces and classes:

```php
use LukaPeharda\TailwindCssColorPalleteGenerator\Color;
use LukaPeharda\TailwindCssColorPalleteGenerator\PalleteGenerator;
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

Lastly, use base color to create a color pallete:

```php
$palleteGenerator = new PalleteGenerator;
$palleteGenerator->setBaseColor($baseColor);
$pallete = $palleteGenerator->getPallete();
```

Generated `$pallete` will be an array where keys are TailwindCSS color steps and values `Color` objects:

```php
$pallete = [
    50 => Color,
    100 => Color,
    200 => Color,
    300 => Color,
    ...
];
```

You can then loop over it to generate CSS variables or use it anyway you see fit:

```php
foreach ($pallete as $key => $color) {
    echo '--color-primary-' . $key . ': #' . $color->getHex() . ';';
}
```

## Defaults

`PalleteGenerator` class has some configurable options set to a sensible defaults.

### Base color step value

By default base color step value is `500`. This means that 5 lighter colors and 4 darker will be generated in a pallete.

You can change it by calling `setBaseValue` method on the `PalleteGenerator` object:

```php
$palleteGenerator->setBaseValue(400);
```

By setting the base value to `400` 4 lighter colors and 5 darker will be generated.

### Bounds thresholds

By default the lightest generated color will have a lightness value of 90% and the darkest will have the lightness value of 10%.

You can change this by calling `setThresholdLightest` and `setThresholdDarkest` methods on `PalleteGenerator` object:

```php
$palleteGenerator->setThresholdLightest(80); // or $palleteGenerator->setThresholdLightest(0.8);
$palleteGenerator->setThresholdDarkest(20); // or $palleteGenerator->setThresholdDarkest(0.2);
```

### Color steps

By default, TailwindCSS 2.x color steps range is used (from `50` to `900`).

You can override it by calling `setColorSteps` method on `PalleteGenerator` object by giving it an array with step values:

```php
$palleteGenerator->setColorSteps([100, 200, 300, 400, 500, 600, 700]);
```

## Todos

Different strategies for generating colors are planned to be developed.