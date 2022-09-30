<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Sylius\Component\Core\Model\ProductInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @mixin ModelFactory
 */
trait WithProductTrait
{
    public function withProduct(Proxy|ProductInterface|string $product): static
    {
        return $this->addState(['product' => $product]);
    }
}
