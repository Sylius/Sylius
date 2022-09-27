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

use Sylius\Bundle\CoreBundle\DataFixtures\Story\DefaultAdminUsersStoryInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class DefaultAdminUsersStoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    public function it_creates_default_admin_users(): void
    {
        /** @var DefaultAdminUsersStoryInterface $defaultAdminUsersStory */
        $defaultAdminUsersStory = self::getContainer()->get('sylius.data_fixtures.story.default_admin_users');

        $defaultAdminUsersStory->build();

        $adminUser = $this->getAdminUserGroupByCode('sylius@example.com');
        $this->assertNotNull($adminUser, sprintf('Admin user "%s" was not found but it should.', 'sylius@example.com'));
        $this->assertEquals('sylius', $adminUser->getUsername());
        $this->assertNotNull($adminUser->getPassword());
        $this->assertTrue($adminUser->isEnabled());
        $this->assertEquals('en_US', $adminUser->getLocaleCode());
        $this->assertEquals('John', $adminUser->getFirstName());
        $this->assertEquals('Doe', $adminUser->getLastName());
        $this->assertEquals('john.jpg', $adminUser->getAvatar()->getFile()->getFilename());

        $adminUser = $this->getAdminUserGroupByCode('api@example.com');
        $this->assertNotNull($adminUser, sprintf('Admin user "%s" was not found but it should.', 'api@example.com'));
        $this->assertEquals('api', $adminUser->getUsername());
        $this->assertNotNull($adminUser->getPassword());
        $this->assertTrue($adminUser->isEnabled());
        $this->assertEquals('en_US', $adminUser->getLocaleCode());
        $this->assertEquals('Luke', $adminUser->getFirstName());
        $this->assertEquals('Brushwood', $adminUser->getLastName());
        $this->assertEquals('luke.jpg', $adminUser->getAvatar()->getFile()->getFilename());
    }

    private function getAdminUserGroupByCode(string $email): ?AdminUserInterface
    {
        /** @var UserRepositoryInterface $userRepository */
        $userRepository = self::getContainer()->get('sylius.repository.admin_user');

        return $userRepository->findOneByEmail($email);
    }
}
