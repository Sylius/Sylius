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

final class ShopExtension extends \Twig_Extension
{
    /**
     * @var bool
     */
    private $isShopEnabled;

    /**
     * @param bool $isShopEnabled
     */
    public function __construct(bool $isShopEnabled)
    {
        $this->isShopEnabled = $isShopEnabled;
    }

    /**
     * @return array|\Twig_Function[]
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_Function('is_shop_enabled', function (): bool {
                return $this->isShopEnabled;
            }),
        ];
    }
}
