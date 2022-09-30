<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Sylius\Component\Core\Model\ProductInterface;
use Zenstruck\Foundry\Proxy;

interface WithProductInterface
{
    public function withProduct(Proxy|ProductInterface|string $product): static;
}
