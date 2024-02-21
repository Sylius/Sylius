<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\UiBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TestFormAttributeExtension extends AbstractExtension
{
    public function __construct(private string $environment)
    {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'sylius_test_form_attribute',
                [$this, 'getTestFormAttribute'],
                ['is_safe' => ['html']],
            ),
            new TwigFunction(
                'sylius_test_form_attributes',
                function (array $attributes): array {
                    if (!str_starts_with($this->environment, 'test')) {
                        return [];
                    }

                    $result = [];

                    foreach ($attributes as $name => $value) {
                        $result[sprintf('data-test-%s', $name)] = (string) $value;
                    }

                    return ['attr' => $result];
                },
                ['is_safe' => ['html']]
            )
        ];
    }

    public function getTestFormAttribute(string $name, ?string $value = null): array
    {
        if (str_starts_with($this->environment, 'test')) {
            return ['attr' => ['data-test-' . $name => (string) $value]];
        }

        return [];
    }
}
