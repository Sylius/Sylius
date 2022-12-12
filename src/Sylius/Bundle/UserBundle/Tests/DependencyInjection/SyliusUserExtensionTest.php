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

namespace Sylius\Bundle\UserBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use PHPUnit\Framework\Assert;
use Sylius\Bundle\UserBundle\DependencyInjection\SyliusUserExtension;
use Sylius\Component\Resource\Factory\Factory;

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
