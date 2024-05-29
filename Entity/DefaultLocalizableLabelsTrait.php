<?php

namespace Pim\Bundle\CustomEntityBundle\Entity;

use Webmozart\Assert\Assert;

trait DefaultLocalizableLabelsTrait
{
    private ?string $label = null;
    private ?array $labels = null;

    public function setLabels(array $labels): void
    {
        foreach ($labels as $locale => $content) {
            $this->setLabel($locale, $content);
        }
    }

    public function setLabel(string $locale, string $content = null): void
    {
        $this->pop();
        Assert::stringNotEmpty($locale);

        // Validate the locale code format
        if (!preg_match('/^[a-z]{2}_[A-Z]{2}$/', $locale)) {
            throw new InvalidArgumentException('Invalid locale code format. Expected format is xx_YY.');
        }

        if (empty($content)) {
            unset($labels[$locale]);
            return;
        }

        $this->labels[$locale] = $content;
    }

    public function getLabel(string $locale):? string
    {
        return $this->labels[$locale] ?? null;
    }

    public function getLabels(): array
    {
        return $this->labels;
    }

    private function unpop(): void
    {
        if ($this->labels === null) {
            $this->labels = [];

            if (is_string($this->label)) {
                $this->labels = json_decode($this->label, true);
            }
        }
    }

    private function pop(): void
    {
        $this->label = json_encode($this->labels);
    }

    public function __destruct()
    {
        $this->pop();
    }
}