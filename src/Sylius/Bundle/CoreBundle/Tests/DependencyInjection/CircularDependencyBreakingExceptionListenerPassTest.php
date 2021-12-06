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

namespace Sylius\Bundle\CoreBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\CircularDependencyBreakingExceptionListenerPass;
use Sylius\Bundle\CoreBundle\EventListener\CircularDependencyBreakingExceptionListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class CircularDependencyBreakingExceptionListenerPassTest extends AbstractCompilerPassTestCase
{
    public function it_register_circular_dependency_breaking_error_listener_when_exception_listener_is_registered(): void
    {
        $this->container->setDefinition('twig.exception_listener', new Definition('ExceptionListener'));

        $this->compile();

        $this->assertContainerBuilderHasService(CircularDependencyBreakingExceptionListener::class);
    }

    public function it_does_nothing_when_exception_listener_is_not_registered(): void
    {
        $this->compile();

        $this->assertContainerBuilderNotHasService(CircularDependencyBreakingExceptionListener::class);
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CircularDependencyBreakingExceptionListenerPass());
    }
}
