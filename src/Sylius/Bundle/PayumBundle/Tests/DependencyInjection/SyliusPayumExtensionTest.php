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

namespace Sylius\Bundle\PayumBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\PayumBundle\DependencyInjection\SyliusPayumExtension;

final class SyliusPayumExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_loads_gateway_config_validation_groups_parameter_value_properly(): void
    {
        $this->load([
            'gateway_config' => [
                'validation_groups' => [
                        'paypal_express_checkout' => ['sylius', 'paypal'],
                        'offline' => ['sylius'],
                ],
            ],
        ]);

        $this->assertContainerBuilderHasParameter('sylius.payum.gateway_config.validation_groups', ['paypal_express_checkout' => ['sylius', 'paypal'], 'offline' => ['sylius']]);
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusPayumExtension()];
    }
}
