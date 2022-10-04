<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Sylius\Component\Core\Model\CustomerInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @mixin ModelFactory
 */
trait WithCustomerTrait
{
    public function withCustomer(Proxy|CustomerInterface|string $customer): self
    {
        return $this->addState(['customer' => $customer]);
    }
}
