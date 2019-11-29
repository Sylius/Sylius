<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
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
    /** @var string */
    private $env;

    public function __construct(string $env)
    {
        $this->env = $env;
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
                    if (strpos($this->env, 'test') === 0) {
                        return ['attr' => ['data-test-'.$name => (string) $value]];
                    }

                    return [];
                },
                ['is_safe' => ['html']]
            ),
        ];
    }
}
