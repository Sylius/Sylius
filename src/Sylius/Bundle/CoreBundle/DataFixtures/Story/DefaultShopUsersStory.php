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
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ShopUserFactoryInterface;
use Zenstruck\Foundry\Story;

final class DefaultShopUsersStory extends Story implements DefaultShopUsersStoryInterface
{
    public function __construct(private ShopUserFactoryInterface $shopUserFactory)
    {
    }

    public function build(): void
    {
        $this->shopUserFactory::createMany(20);

        $this->shopUserFactory::new()
            ->withEmail('shop@example.com')
            ->withFirstName('John')
            ->withLastName('Doe')
            ->withPassword('sylius')
            ->create()
        ;
    }
}
