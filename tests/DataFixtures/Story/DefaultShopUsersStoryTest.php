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

namespace Sylius\Tests\DataFixtures\Story;

use Sylius\Bundle\CoreBundle\DataFixtures\Story\DefaultCustomerGroupsStoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Story\DefaultShopUsersStoryInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class DefaultShopUsersStoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    public function it_creates_default_shop_users(): void
    {
        /** @var DefaultShopUsersStoryInterface $defaultShopUsersStory */
        $defaultShopUsersStory = self::getContainer()->get('sylius.data_fixtures.story.default_shop_users');

        $defaultShopUsersStory->build();

        $shopUser = $this->getShopUserByEmail('shop@example.com');
        $this->assertNotNull($shopUser, sprintf('Shop user "%s" was not found but it should.', 'shop@example.com'));
        $this->assertNotNull($shopUser->getCustomer());
        $this->assertEquals('John', $shopUser->getCustomer()->getFirstName());
        $this->assertEquals('Doe', $shopUser->getCustomer()->getLastName());
        $this->assertNotNull($shopUser->getPassword());
    }

    private function getShopUserByEmail(string $email): ?ShopUserInterface
    {
        /** @var UserRepositoryInterface $shopUserRepository */
        $shopUserRepository = self::getContainer()->get('sylius.repository.shop_user');

        return $shopUserRepository->findOneByEmail($email);
    }
}
