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

use Sylius\Bundle\CoreBundle\ShopFixtures\Command\CreateManyAddressesInterface;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Factory\AddressFactory;

final class CreateManyAddressesHandler
{
    public function __invoke(CreateManyAddressesInterface $command): array
    {
        return AddressFactory::new()
            ->withAttributes($command->toArray())
            ->many($command->min(), $command->max())
            ->create()
        ;
    }
}
