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

trigger_deprecation(
    'sylius/ui-bundle',
    '1.14',
    'The "%s" class is deprecated and will be removed in Sylius 2.0. Use sylius/twig-extra package instead.',
    TestFormAttributeExtension::class,
);
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
                function (string $name, ?string $value = null): array {
                    if (str_starts_with($this->environment, 'test')) {
                        return ['attr' => ['data-test-' . $name => (string) $value]];
                    }

                    return [];
                },
                ['is_safe' => ['html']],
            ),
        ];
    }
}
