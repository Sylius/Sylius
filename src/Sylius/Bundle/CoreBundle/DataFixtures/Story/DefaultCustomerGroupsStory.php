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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Story;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ChannelFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CustomerGroupFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\PaymentMethodFactoryInterface;
use Zenstruck\Foundry\Story;

final class DefaultCustomerGroupsStory extends Story implements DefaultCustomerGroupsStoryInterface
{
    public function __construct(private CustomerGroupFactoryInterface $customerGroupFactory)
    {
    }

    public function build(): void
    {
        $this->customerGroupFactory::new()
            ->withCode('retail')
            ->withName('Retail')
            ->create()
        ;

        $this->customerGroupFactory::new()
            ->withCode('wholesale')
            ->withName('Wholesale')
            ->create()
        ;
    }
}
