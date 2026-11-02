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

namespace Sylius\Bundle\AdminBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/** @experimental */
final class LiveGridExtension extends AbstractExtension
{
    public function __construct(private bool $isLiveGridEnabled)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_live_grid_enabled', fn (): bool => $this->isLiveGridEnabled),
        ];
    }
}
