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

namespace Sylius\Bundle\AdminBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ShopExtension extends AbstractExtension
{
    private bool $isShopEnabled;

    public function __construct(bool $isShopEnabled)
    {
        $this->isShopEnabled = $isShopEnabled;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_shop_enabled', fn (): bool => $this->isShopEnabled),
        ];
    }
}
