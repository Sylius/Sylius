<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\State;

use Sylius\Component\Core\Model\CustomerInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @mixin ModelFactory
 */
trait WithoutCustomerTrait
{
    public function withoutCustomer(): self
    {
        return $this->addState(['customer' => null]);
    }
}
