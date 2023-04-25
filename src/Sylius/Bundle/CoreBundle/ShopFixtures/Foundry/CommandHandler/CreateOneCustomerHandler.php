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

use Sylius\Bundle\CoreBundle\ShopFixtures\Command\CreateOneCustomerInterface;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Factory\CustomerFactory;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Zenstruck\Foundry\Proxy;

#[AsMessageHandler]
final class CreateOneCustomerHandler
{
    public function __invoke(CreateOneCustomerInterface $command): CustomerInterface|Proxy
    {
        return CustomerFactory::createOne($command->toArray());
    }
}
