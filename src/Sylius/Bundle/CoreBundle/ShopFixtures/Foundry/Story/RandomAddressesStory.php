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

namespace Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Story;

use Sylius\Bundle\CoreBundle\ShopFixtures\Story\RandomAddressesStoryInterface;
use Zenstruck\Foundry\Story;

final class RandomAddressesStory extends Story
{
    public function __construct(private RandomAddressesStoryInterface $randomAddressesStory)
    {
    }

    public function build(): void
    {
        $this->randomAddressesStory->create();
    }
}
