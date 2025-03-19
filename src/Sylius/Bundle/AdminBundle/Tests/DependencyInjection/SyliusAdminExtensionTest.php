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

namespace Sylius\Bundle\AdminBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\AdminBundle\DependencyInjection\SyliusAdminExtension;

final class SyliusAdminExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_loads_notifications_hub_enabled_parameter_value_properly(): void
    {
        $this->load(['notifications' => ['hub_enabled' => true]]);
        $this->assertContainerBuilderHasParameter('sylius.admin.notification.hub_enabled', true);

        $this->load(['notifications' => ['hub_enabled' => false]]);
        $this->assertContainerBuilderHasParameter('sylius.admin.notification.hub_enabled', false);
    }

    /** @test */
    public function it_loads_twig_payment_method_excluded_gateways_properly(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->load(['twig' => ['payment_method' => ['excluded_gateways' => ['offline-test']]]]);

        $this->assertContainerBuilderHasParameter('sylius.admin.twig.payment_method.excluded_gateways', ['offline-test']);
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusAdminExtension()];
    }
}
