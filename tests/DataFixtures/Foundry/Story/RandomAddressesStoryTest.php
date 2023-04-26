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

namespace Sylius\Tests\DataFixtures\Foundry\Story;

use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Story\RandomAddressesStory;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class RandomAddressesStoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_addresses(): void
    {
        self::bootKernel();

        RandomAddressesStory::load();

        $addresses = $this->getAddressRepository()->findAll();

        $this->assertCount(10, $addresses);
    }

    private function getAddressRepository(): RepositoryInterface
    {
        return self::getContainer()->get('sylius.repository.address');
    }
}
