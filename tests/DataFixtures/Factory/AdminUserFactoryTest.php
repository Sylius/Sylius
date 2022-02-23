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

namespace Sylius\Tests\DataFixtures\Factory;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\AdminUserFactory;
use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class AdminUserFactoryTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    function it_creates_admin_user_with_random_data(): void
    {
        $adminUser = AdminUserFactory::createOne();

        $this->assertInstanceOf(AdminUserInterface::class, $adminUser->object());
        $this->assertNotNull($adminUser->getEmail());
        $this->assertNotNull($adminUser->getUsername());
        $this->assertTrue($adminUser->isEnabled());
        $this->assertNotNull($adminUser->getPassword());
        $this->assertNotNull($adminUser->getLocaleCode());
        $this->assertNull($adminUser->getFirstName());
        $this->assertNull($adminUser->getLastName());
        $this->assertTrue($adminUser->hasRole('ROLE_ADMINISTRATION_ACCESS'));
        $this->assertFalse($adminUser->hasRole('ROLE_API_ACCESS'));
        $this->assertNull($adminUser->getAvatar());
    }

    /** @test */
    function it_creates_admin_user_with_given_email(): void
    {
        $adminUser = AdminUserFactory::new()->withEmail('dark.vader@starwars.com')->create();

        $this->assertEquals('dark.vader@starwars.com', $adminUser->getEmail());
    }

    /** @test */
    function it_creates_admin_user_with_given_username(): void
    {
        $adminUser = AdminUserFactory::new()->withUsername('vadoo')->create();

        $this->assertEquals('vadoo', $adminUser->getUsername());
    }

    /** @test */
    function it_creates_enabled_admin_user(): void
    {
        $adminUser = AdminUserFactory::new()->enabled()->create();

        $this->assertTrue($adminUser->isEnabled());
    }

    /** @test */
    function it_creates_disabled_admin_user(): void
    {
        $adminUser = AdminUserFactory::new()->disabled()->create();

        $this->assertFalse($adminUser->isEnabled());
    }

    /** @test */
    function it_creates_admin_user_with_given_password(): void
    {
        $adminUser = AdminUserFactory::new()->withPassword('luke-is-my-son')->withoutPersisting()->create();

        $this->assertEquals('luke-is-my-son', $adminUser->getPlainPassword());
    }

    /** @test */
    function it_creates_admin_user_with_api_access(): void
    {
        $adminUser = AdminUserFactory::new()->withApiAccess()->create();

        $this->assertTrue($adminUser->hasRole('ROLE_API_ACCESS'));
    }

    /** @test */
    function it_creates_admin_user_with_given_first_name(): void
    {
        $adminUser = AdminUserFactory::new()->withFirstName('Dark')->create();

        $this->assertEquals('Dark', $adminUser->getFirstName());
    }

    /** @test */
    function it_creates_admin_user_with_given_last_name(): void
    {
        $adminUser = AdminUserFactory::new()->withLastName('Vader')->create();

        $this->assertEquals('Vader', $adminUser->getLastName());
    }

    /** @test */
    function it_creates_admin_user_with_given_avatar(): void
    {
        $adminUser = AdminUserFactory::new()->withAvatar('@SyliusCoreBundle/Resources/fixtures/adminAvatars/luke.jpg')->create();

        $this->assertNotNull($adminUser->getAvatar());
        $this->assertStringEndsWith('luke.jpg', $adminUser->getAvatar()->getPath());
    }
}
