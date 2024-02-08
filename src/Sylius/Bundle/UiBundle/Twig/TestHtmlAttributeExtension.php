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

final class TestHtmlAttributeExtension extends AbstractExtension
{
    public function __construct(private string $env)
    {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'sylius_test_html_attribute',
                function (string $name, ?string $value = null): string {
                    if (str_starts_with($this->env, 'test')) {
                        return sprintf('data-test-%s="%s"', $name, (string) $value);
                    }

                    return '';
                },
                ['is_safe' => ['html']],
            ),
        ];
    }
}
