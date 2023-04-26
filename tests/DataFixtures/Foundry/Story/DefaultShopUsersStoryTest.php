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

use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Story\DefaultShopUsersStory;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class DefaultShopUsersStoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_shop_users(): void
    {
        self::bootKernel();

        DefaultShopUsersStory::load();

        $shopUsers = $this->getShopUserRepository()->findAll();

        $this->assertCount(21, $shopUsers);

        $shopUser = $this->findShopUserByEmail('test@example.com');
        $this->assertEquals('John', $shopUser->getCustomer()->getFirstName());
        $this->assertEquals('Doe', $shopUser->getCustomer()->getLastName());
    }

    private function findShopUserByEmail(string $email): ShopUserInterface
    {
        $shopUser = $this->getShopUserRepository()->findOneByEmail($email);

        $this->assertNotNull($shopUser, sprintf('Shop user %s was not found.', $email));

        return $shopUser;
    }

    private function getShopUserRepository(): UserRepositoryInterface
    {
        return self::getContainer()->get('sylius.repository.shop_user');
    }
}
