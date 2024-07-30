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

namespace Sylius\Bundle\AddressingBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\AddressingBundle\DependencyInjection\SyliusAddressingExtension;

final class SyliusAddressingExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_loads_zone_member_validation_groups_parameter_value_properly(): void
    {
        $this->load([
            'zone_member' => [
                'validation_groups' => [
                    'country' => ['sylius', 'country'],
                    'zone' => ['sylius', 'zone'],
                ],
            ],
        ]);

        $this->assertContainerBuilderHasParameter(
            'sylius.addressing.zone_member.validation_groups',
            ['country' => ['sylius', 'country'], 'zone' => ['sylius', 'zone']],
        );
    }

    /** @test */
    public function it_loads_empty_zone_member_validation_groups_parameter_value(): void
    {
        $this->load();

        $this->assertContainerBuilderHasParameter(
            'sylius.addressing.zone_member.validation_groups',
            [],
        );
    }

    protected function getContainerExtensions(): array
    {
        return [
            new SyliusAddressingExtension(),
        ];
    }
}
