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

use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Factory\CurrencyFactory;
use Sylius\Bundle\CoreBundle\ShopFixtures\Command\CreateOneCurrencyInterface;
use Sylius\Component\Currency\Model\Currency;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Zenstruck\Foundry\Proxy;

#[AsMessageHandler]
final class CreateOneCurrencyHandler
{
    public function __invoke(CreateOneCurrencyInterface $command): Currency|Proxy
    {
        return CurrencyFactory::createOne($command->toArray());
    }
}
