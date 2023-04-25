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

use Sylius\Bundle\CoreBundle\ShopFixtures\Command\CreateOneCountryInterface;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Factory\CountryFactory;
use Sylius\Component\Addressing\Model\Country;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Zenstruck\Foundry\Proxy;

#[AsMessageHandler]
final class CreateOneCountryHandler
{
    public function __invoke(CreateOneCountryInterface $command): Country|Proxy
    {
        return CountryFactory::createOne($command->toArray());
    }
}
