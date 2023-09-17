<?php

declare(strict_types=1);

namespace Sylius\Bundle\UiBundle\Tests\Functional\src;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent]
final class SomeTwigComponent
{
    public array $context = [];

    #[ExposeInTemplate]
    public function getContextAsString(): string
    {
        if ([] === $this->context) {
            return 'no context';
        }

        $result = [];

        foreach ($this->context as $key => $value) {
            $result[] = $key . '=' . $value;
        }

        return implode(', ', $result);
    }
}
