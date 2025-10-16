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

namespace Tests\Sylius\Bundle\LocaleBundle\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Bundle\LocaleBundle\Attribute\AsLocaleContext;
use Sylius\Bundle\LocaleBundle\DependencyInjection\SyliusLocaleExtension;
use Symfony\Component\DependencyInjection\Definition;
use Tests\Sylius\Bundle\LocaleBundle\Stub\LocaleContextStub;

final class SyliusLocaleExtensionTest extends AbstractExtensionTestCase
{
    #[Test]
    public function it_autoconfigures_locale_context_with_attribute(): void
    {
        $this->container->setDefinition(
            'acme.locale_context_with_attribute',
            (new Definition())
                ->setClass(LocaleContextStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.locale_context_with_attribute',
            AsLocaleContext::SERVICE_TAG,
            ['priority' => 15],
        );
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusLocaleExtension()];
    }
}
