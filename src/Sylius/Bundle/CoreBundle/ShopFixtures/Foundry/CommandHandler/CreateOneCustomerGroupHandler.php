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

namespace Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\CommandHandler;

use Sylius\Bundle\CoreBundle\ShopFixtures\Command\CreateOneCustomerGroupInterface;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Factory\CustomerGroupFactory;
use Sylius\Component\Customer\Model\CustomerGroup;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Zenstruck\Foundry\Proxy;

#[AsMessageHandler]
final class CreateOneCustomerGroupHandler
{
    public function __invoke(CreateOneCustomerGroupInterface $command): CustomerGroup|Proxy
    {
        return CustomerGroupFactory::createOne($command->toArray());
    }
}
