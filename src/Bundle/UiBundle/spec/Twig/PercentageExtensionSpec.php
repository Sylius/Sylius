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

namespace spec\Sylius\Bundle\UiBundle\Twig;

use PhpSpec\ObjectBehavior;
use Twig\Extension\ExtensionInterface;

final class PercentageExtensionSpec extends ObjectBehavior
{
    function it_is_twig_extension(): void
    {
        $this->shouldImplement(ExtensionInterface::class);
    }

    function it_returns_float_number_as_percentage(): void
    {
        $this->getPercentage(0.112)->shouldReturn('11.2 %');
    }
}
