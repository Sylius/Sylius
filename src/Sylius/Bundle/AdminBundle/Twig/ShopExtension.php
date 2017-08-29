<?php

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
            new \Twig_Function('is_shop_enabled', function(): bool {
                return $this->isShopEnabled;
            })
        ];
    }
}
