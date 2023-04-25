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

use Sylius\Bundle\CoreBundle\ShopFixtures\Command\CreateOneShopUserInterface;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Factory\ShopUserFactory;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Zenstruck\Foundry\Proxy;

#[AsMessageHandler]
final class CreateOneShopUserHandler
{
    public function __invoke(CreateOneShopUserInterface $command): ShopUserInterface|Proxy
    {
        return ShopUserFactory::createOne($command->toArray());
    }
}
