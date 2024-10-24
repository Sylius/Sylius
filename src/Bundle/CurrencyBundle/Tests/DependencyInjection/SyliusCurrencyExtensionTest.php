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

namespace Sylius\Bundle\CurrencyBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\CurrencyBundle\Attribute\AsCurrencyContext;
use Sylius\Bundle\CurrencyBundle\DependencyInjection\SyliusCurrencyExtension;
use Sylius\Bundle\CurrencyBundle\Tests\Stub\CurrencyContextStub;
use Symfony\Component\DependencyInjection\Definition;

final class SyliusCurrencyExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_autoconfigures_currency_context_with_attribute(): void
    {
        $this->container->setDefinition(
            'acme.currency_context_with_attribute',
            (new Definition())
                ->setClass(CurrencyContextStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.currency_context_with_attribute',
            AsCurrencyContext::SERVICE_TAG,
            ['priority' => 15],
        );
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusCurrencyExtension()];
    }
}
