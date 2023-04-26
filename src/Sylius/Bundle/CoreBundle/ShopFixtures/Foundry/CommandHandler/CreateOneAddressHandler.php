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

use Sylius\Bundle\CoreBundle\ShopFixtures\Command\CreateOneAddressInterface;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Factory\AddressFactory;
use Sylius\Component\Core\Model\AddressInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Zenstruck\Foundry\Proxy;

#[AsMessageHandler]
final class CreateOneAddressHandler
{
    public function __invoke(CreateOneAddressInterface $command): AddressInterface|Proxy
    {
        return AddressFactory::createOne($command->toArray());
    }
}
