<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\Component\Core\Model;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\AdminUser;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\User\Model\User;
use Sylius\Component\User\Model\UserInterface;

final class AdminUserTest extends TestCase
{
    private ImageInterface&MockObject $image;

    private AdminUser $adminUser;

    protected function setUp(): void
    {
        $this->image = $this->createMock(ImageInterface::class);
        $this->adminUser = new AdminUser();
    }

    public function testShouldExtendBaseUserModel(): void
    {
        $this->assertInstanceOf(User::class, $this->adminUser);
    }

    public function testShouldImplementAdminUserInterface(): void
    {
        $this->assertInstanceOf(AdminUserInterface::class, $this->adminUser);
    }

    public function testShouldImplementUserInterface(): void
    {
        $this->assertInstanceOf(UserInterface::class, $this->adminUser);
    }

    public function testShouldHaveFirstName(): void
    {
        $this->adminUser->setFirstName('John');

        $this->assertSame('John', $this->adminUser->getFirstName());
    }

    public function testShouldHaveLastname(): void
    {
        $this->adminUser->setLastName('Doe');

        $this->assertSame('Doe', $this->adminUser->getLastname());
    }

    public function testShouldNotHaveLocaleCodeByDefault(): void
    {
        $this->assertNull($this->adminUser->getLocaleCode());
    }

    public function testShouldLocaleCodeBeMutable(): void
    {
        $this->adminUser->setLocaleCode('en_US');

        $this->assertSame('en_US', $this->adminUser->getLocaleCode());
    }

    public function testShouldNotHaveAvatarByDefault(): void
    {
        $this->assertNull($this->adminUser->getAvatar());
    }

    public function testShouldImageBeMutable(): void
    {
        $this->image->expects($this->once())->method('setOwner')->with($this->adminUser);

        $this->adminUser->setImage($this->image);

        $this->assertSame($this->image, $this->adminUser->getImage());
    }

    public function testShouldImageBeNullable(): void
    {
        $this->adminUser->setImage(null);

        $this->assertNull($this->adminUser->getImage());
    }

    public function testShouldAvatarBeAnImage(): void
    {
        $this->adminUser->setAvatar($this->image);

        $this->assertSame($this->image, $this->adminUser->getAvatar());
    }

    public function testShouldHaveAdministrationAccessByDefault(): void
    {
        $this->assertTrue($this->adminUser->hasAdministrationAccess());
        $this->assertTrue($this->adminUser->hasRole(AdminUserInterface::DEFAULT_ADMIN_ROLE));
    }

    public function testShouldNotHaveApiAccessByDefault(): void
    {
        $this->assertFalse($this->adminUser->hasApiAccess());
        $this->assertFalse($this->adminUser->hasRole(AdminUserInterface::API_ACCESS_ROLE));
    }

    public function testShouldAdministrationAccessBeMutable(): void
    {
        $this->adminUser->setAdministrationAccess(false);

        $this->assertFalse($this->adminUser->hasAdministrationAccess());
        $this->assertFalse($this->adminUser->hasRole(AdminUserInterface::DEFAULT_ADMIN_ROLE));

        $this->adminUser->setAdministrationAccess(true);

        $this->assertTrue($this->adminUser->hasAdministrationAccess());
        $this->assertTrue($this->adminUser->hasRole(AdminUserInterface::DEFAULT_ADMIN_ROLE));
    }

    public function testShouldApiAccessBeMutable(): void
    {
        $this->adminUser->setApiAccess(true);

        $this->assertTrue($this->adminUser->hasApiAccess());
        $this->assertTrue($this->adminUser->hasRole(AdminUserInterface::API_ACCESS_ROLE));

        $this->adminUser->setApiAccess(false);

        $this->assertFalse($this->adminUser->hasApiAccess());
        $this->assertFalse($this->adminUser->hasRole(AdminUserInterface::API_ACCESS_ROLE));
    }

    public function testShouldAccessLevelsBeIndependent(): void
    {
        $this->adminUser->setApiAccess(true);
        $this->adminUser->setAdministrationAccess(false);

        $this->assertFalse($this->adminUser->hasAdministrationAccess());
        $this->assertTrue($this->adminUser->hasApiAccess());
    }

    public function testShouldNotDuplicateRolesWhenAccessIsGrantedTwice(): void
    {
        $this->adminUser->setAdministrationAccess(true);
        $this->adminUser->setApiAccess(true);
        $this->adminUser->setApiAccess(true);

        $this->assertSame(
            [AdminUserInterface::DEFAULT_ADMIN_ROLE, AdminUserInterface::API_ACCESS_ROLE],
            $this->adminUser->getRoles(),
        );
    }
}
