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

namespace Sylius\Bundle\UserBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use PHPUnit\Framework\Assert;
use Sylius\Bundle\UserBundle\DependencyInjection\SyliusUserExtension;
use Sylius\Bundle\UserBundle\EventListener\UserLastLoginSubscriber;
use Sylius\Component\User\Model\User;
use Sylius\Resource\Factory\Factory;

final class SyliusUserExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_creates_default_resource_factory_by_default(): void
    {
        $this->load([
            'resources' => [
                'admin' => [
                    'user' => [],
                ],
            ],
        ]);

        $factoryDefinition = $this->container->getDefinition('sylius.factory.admin_user');

        Assert::assertSame(Factory::class, $factoryDefinition->getClass());
    }

    /** @test */
    public function it_creates_last_login_subscriber_for_each_user_type(): void
    {
        $this->load([
            'resources' => [
                'admin' => [
                    'user' => [
                        'login_tracking_interval' => 'P1D',
                        'classes' => [
                            'model' => 'AdminUserClass',
                        ],
                    ],
                ],
                'custom' => [],
            ],
        ]);

        $adminLastLoginSubscriber = $this->container->getDefinition('sylius.listener.admin_user_last_login');
        Assert::assertSame(UserLastLoginSubscriber::class, $adminLastLoginSubscriber->getClass());
        Assert::assertSame('AdminUserClass', $adminLastLoginSubscriber->getArgument(1));
        Assert::assertEquals('P1D', $adminLastLoginSubscriber->getArgument(2));

        $customLastLoginSubscriber = $this->container->getDefinition('sylius.listener.custom_user_last_login');
        Assert::assertSame(UserLastLoginSubscriber::class, $customLastLoginSubscriber->getClass());
        Assert::assertSame(User::class, $customLastLoginSubscriber->getArgument(1));
        Assert::assertNull($customLastLoginSubscriber->getArgument(2));
    }

    /** @test */
    public function it_creates_default_resetting_token_parameters_for_each_user_type(): void
    {
        $this->load([
            'resources' => [
                'admin' => [
                    'user' => [],
                ],
                'shop' => [
                    'user' => [],
                ],
            ],
        ]);

        Assert::assertSame('P1D', $this->container->getParameter('sylius.admin_user.token.password_reset.ttl'));
        Assert::assertSame('P1D', $this->container->getParameter('sylius.shop_user.token.password_reset.ttl'));
    }

    /** @test */
    public function it_creates_custom_resetting_token_parameters_for_each_user_type(): void
    {
        $this->load([
            'resources' => [
                'admin' => [
                    'user' => [
                        'resetting' => [
                            'token' => [
                                'ttl' => 'P5D',
                            ],
                        ],
                    ],
                ],
                'shop' => [
                    'user' => [
                        'resetting' => [
                            'token' => [
                                'ttl' => 'P2D',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        Assert::assertSame('P5D', $this->container->getParameter('sylius.admin_user.token.password_reset.ttl'));
        Assert::assertSame('P2D', $this->container->getParameter('sylius.shop_user.token.password_reset.ttl'));
    }

    protected function getContainerExtensions(): array
    {
        return [
            new SyliusUserExtension(),
        ];
    }
}
