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

namespace Sylius\Bundle\AttributeBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\AttributeBundle\Attribute\AsAttributeType;
use Sylius\Bundle\AttributeBundle\DependencyInjection\SyliusAttributeExtension;
use Sylius\Bundle\AttributeBundle\Tests\Stub\AttributeTypeStub;
use Symfony\Component\DependencyInjection\Definition;

final class SyliusAttributeExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_autoconfigures_attribute_type_with_attribute(): void
    {
        $this->container->setDefinition(
            'acme.attribute_type_with_attribute',
            (new Definition())
                ->setClass(AttributeTypeStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.attribute_type_with_attribute',
            AsAttributeType::SERVICE_TAG,
            [
                'attribute-type' => 'test',
                'label' => 'Test',
                'form-type' => 'SomeFormType',
                'priority' => 15,
            ],
        );
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusAttributeExtension()];
    }
}
