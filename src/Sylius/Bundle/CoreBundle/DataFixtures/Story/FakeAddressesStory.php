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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\AddressFactoryInterface;
use Zenstruck\Foundry\Story;

final class FakeAddressesStory extends Story implements FakeAddressesStoryInterface
{
    public function __construct(private AddressFactoryInterface $addressFactory)
    {
    }

    public function build(): void
    {
        $this->addressFactory::createMany(10);

        $this->addressFactory::new()->withCountryCode('US')->create();
    }
}
