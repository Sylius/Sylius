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

namespace Sylius\Bundle\UserBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\UserBundle\DependencyInjection\Compiler\RemoveUserPasswordEncoderPass;
use Sylius\Bundle\UserBundle\Security\UserPasswordEncoder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

final class RemoveUserPasswordEncoderPassTest extends AbstractCompilerPassTestCase
{
    /** @test */
    public function it_removes_user_password_encoder_definition_on_symfony_up_to_6(): void
    {
        if (interface_exists(EncoderFactoryInterface::class)) {
            $this->markTestSkipped('Password encoder should not be removed on Symfony < 6');
        }

        $this->registerService('sylius.security.password_encoder', UserPasswordEncoder::class);

        $this->compile();

        $this->assertContainerBuilderNotHasService('sylius.security.password_encoder');
    }

    /** @test */
    public function it_does_not_remove_user_password_encoder_definition_on_symfony_lower_than_6(): void
    {
        if (!interface_exists(EncoderFactoryInterface::class)) {
            $this->markTestSkipped('Password encoder should be removed on Symfony >= 6');
        }

        $this->registerService('sylius.security.password_encoder', UserPasswordEncoder::class);

        $this->compile();

        $this->assertContainerBuilderHasService('sylius.security.password_encoder');
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RemoveUserPasswordEncoderPass());
    }
}
