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

namespace Tests\Sylius\Bundle\AttributeBundle\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Bundle\AttributeBundle\Attribute\AsAttributeType;
use Sylius\Bundle\AttributeBundle\DependencyInjection\SyliusAttributeExtension;
use Symfony\Component\DependencyInjection\Definition;
use Tests\Sylius\Bundle\AttributeBundle\Stub\AttributeTypeStub;

final class SyliusAttributeExtensionTest extends AbstractExtensionTestCase
{
    #[Test]
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
                'attribute_type' => 'test',
                'label' => 'Test',
                'form_type' => 'SomeFormType',
                'priority' => 15,
                'configuration_form_type' => null,
            ],
        );
    }

    #[Test]
    public function it_autoconfigures_attribute_type_with_attribute_configuration(): void
    {
        $this->container->setDefinition(
            'acme.attribute_type_with_attribute_configuration',
            (new Definition())
                ->setClass(AttributeTypeStub::class)
                ->setAutoconfigured(false)
                ->setTags(['sylius.attribute.type' => [
                    [
                        'attribute_type' => 'test',
                        'label' => 'Test',
                        'form_type' => 'SomeFormType',
                        'priority' => 15,
                        'configuration_form_type' => 'SomeConfigurationFormType',
                    ],
                ]]),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.attribute_type_with_attribute_configuration',
            AsAttributeType::SERVICE_TAG,
            [
                'attribute_type' => 'test',
                'label' => 'Test',
                'form_type' => 'SomeFormType',
                'priority' => 15,
                'configuration_form_type' => 'SomeConfigurationFormType',
            ],
        );
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusAttributeExtension()];
    }
}
