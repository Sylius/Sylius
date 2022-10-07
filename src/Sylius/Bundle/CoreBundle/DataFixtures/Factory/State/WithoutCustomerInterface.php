<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\State;

use Sylius\Component\Core\Model\CustomerInterface;
use Zenstruck\Foundry\Proxy;

interface WithoutCustomerInterface
{
    /**
     * @return $this
     */
    public function withoutCustomer(): self;
}
