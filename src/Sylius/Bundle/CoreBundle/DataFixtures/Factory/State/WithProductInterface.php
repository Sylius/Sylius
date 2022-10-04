<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\State;

use Sylius\Component\Core\Model\ProductInterface;
use Zenstruck\Foundry\Proxy;

interface WithProductInterface
{
    /**
     * @return $this
     */
    public function withProduct(Proxy|ProductInterface|string $product): self;
}
