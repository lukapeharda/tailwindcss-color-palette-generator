<?php

namespace LukaPeharda\TailwindCssColorPalleteGenerator;

class PalleteGenerator
{
    /**
     * @var Color
     */
    protected $baseColor;

    /**
     * @var int
     */
    protected $baseValue = 500;

    /**
     * Threshold for lightness value for lightest color.
     * @var float
     */
    protected $thresholdLightest = 0.9;

    /**
     * Threshold for lightness value for darkest color.
     *
     * @var float
     */
    protected $thresholdDarkest = 0.1;

    /**
     * TailwindCSS color steps.
     *
     * @var array
     */
    protected $colorSteps = [50, 100, 200, 300, 400, 500, 600, 700, 800, 900];

    /**
     * Set pallete base color. Hue, saturation and lightness will be
     * calculated from this color.
     *
     * @param   Color  $color
     *
     * @return  self
     */
    public function setBaseColor(Color $color): self
    {
        $this->baseColor = $color;

        return $this;
    }

    /**
     * Set base color value from TailwindCSS pallete steps range.
     *
     * By default set to 500.
     *
     * @param   int   $value
     *
     * @return  self
     */
    public function setBaseValue(int $value): self
    {
        $this->baseValue = $value;

        return $this;
    }

    /**
     * Set threshold value for lightest color lightness upper bound.
     *
     * Decimal value of maximal ligthness percentage.
     *
     * @param   float  $threshold
     *
     * @return  self
     */
    public function setThresholdLightest(float $threshold): self
    {
        $this->thresholdLightest = $threshold > 1 ? $threshold / 100 : $threshold;

        return $this;
    }

    /**
     * Set threshold value for darkest color lightness lower bound.
     *
     * Decimal value of minimal ligthness percentage.
     *
     * @param   float  $threshold
     *
     * @return  self
     */
    public function setThresholdDarkest(float $threshold): self
    {
        $this->thresholdDarkest = $threshold > 1 ? $threshold / 100 : $threshold;

        return $this;
    }

    public function setColorSteps(array $steps): self
    {
        $this->colorSteps = $steps;

        return $this;
    }

    /**
     * Generate pallete.
     *
     * HSL values will be read from the base color.
     *
     * Lighter and darker colors will be generated by lowering or raisng the
     * lightness factor using defined color steps.
     *
     * @return  array
     */
    protected function generatePallete(): array
    {
        $baseColorHsl = $this->baseColor->getHsl();

        $pallete = [];

        $lighterSteps = $this->getLighterSteps();

        $lighterRangeStep = ($this->thresholdLightest - $baseColorHsl[Color::LIGHTNESS]) / count($lighterSteps);

        foreach (array_reverse($lighterSteps) as $index => $step) {
            $pallete[$step] = Color::fromHsl(
                $baseColorHsl[Color::HUE],
                $baseColorHsl[Color::SATURATION],
                $baseColorHsl[Color::LIGHTNESS] + ($lighterRangeStep * ($index + 1)),
            );
        }

        $pallete = array_reverse($pallete, true);

        $pallete[$this->baseValue] = $this->baseColor;

        $darkerSteps = $this->getDarkerSteps();

        $darkerRangeStep = ($baseColorHsl[Color::LIGHTNESS] - $this->thresholdDarkest) / count($darkerSteps);

        foreach ($darkerSteps as $index => $step) {
            $pallete[$step] = Color::fromHsl(
                $baseColorHsl[Color::HUE],
                $baseColorHsl[Color::SATURATION],
                $baseColorHsl[Color::LIGHTNESS] - ($darkerRangeStep * ($index + 1)),
            );
        }

        return $pallete;
    }

    /**
     * Return pallete as array.
     *
     * @return  array
     */
    public function getPallete(): array
    {
        return $this->generatePallete();
    }

    /**
     * Return steps which are higher than base.
     *
     * @return  array
     */
    protected function getDarkerSteps(): array
    {
        return array_values(array_filter($this->colorSteps, function($step) {
            return $step > $this->baseValue;
        }));
    }

    /**
     * Return steps which are lower than base.
     *
     * @return  array
     */
    protected function getLighterSteps(): array
    {
        return array_values(array_filter($this->colorSteps, function($step) {
            return $step < $this->baseValue;
        }));
    }
}