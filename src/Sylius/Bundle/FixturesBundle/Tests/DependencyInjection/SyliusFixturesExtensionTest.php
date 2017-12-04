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

namespace Sylius\Bundle\FixturesBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\FixturesBundle\DependencyInjection\SyliusFixturesExtension;

final class SyliusFixturesExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function it_does_not_crash_if_no_suite_is_configured(): void
    {
        $this->load();
    }

    /**
     * @test
     */
    public function it_registers_configured_suites(): void
    {
        $this->load(['suites' => [
            'suite_name' => [],
        ]]);

        $suiteRegistryDefinition = $this->container->findDefinition('sylius_fixtures.suite_registry');
        $suiteMethodCall = $suiteRegistryDefinition->getMethodCalls()[0];

        static::assertSame('addSuite', $suiteMethodCall[0]);
        static::assertSame('suite_name', $suiteMethodCall[1][0]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions(): array
    {
        return [new SyliusFixturesExtension()];
    }
}
