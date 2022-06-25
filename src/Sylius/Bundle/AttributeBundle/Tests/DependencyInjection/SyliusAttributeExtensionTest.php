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

namespace Sylius\Bundle\AttributeBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\AttributeBundle\DependencyInjection\SyliusAttributeExtension;
use Sylius\Component\Attribute\Attribute\AsAttributeType;
use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class SyliusAttributeExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_autoconfigures_attributes_types_with_attribute(): void
    {
        $this->container->register(
            'acme.attribute_type_autoconfigured',
            DummyAttributeType::class
        )->setAutoconfigured(true);

        $this->container->register(
            'acme.attribute_type_with_configuration_form_type_autoconfigured',
            DummyAttributeTypeWithConfigurationFormType::class
        )->setAutoconfigured(true);

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.attribute_type_autoconfigured',
            'sylius.attribute.type',
            [
                'attribute_type' => 'dummy',
                'label' => 'dummy',
                'form_type' => 'DummyType',
                'configuration_form_type' => null
            ]
        );

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.attribute_type_with_configuration_form_type_autoconfigured',
            'sylius.attribute.type',
            [
                'attribute_type' => 'dummy',
                'label' => 'dummy',
                'form_type' => 'DummyType',
                'configuration_form_type' => 'DummyAttributeConfigurationType'
            ]
        );
    }

    protected function getContainerExtensions(): array
    {
        return [
            new SyliusAttributeExtension(),
        ];
    }
}

#[AsAttributeType(attributeType: 'dummy', label: 'dummy', formType: 'DummyType')]
class DummyAttributeType implements AttributeTypeInterface
{
    public function getStorageType(): string
    {
        return 'default';
    }

    public function getType(): string
    {
        return 'dummy';
    }

    public function validate(AttributeValueInterface $attributeValue, ExecutionContextInterface $context, array $configuration): void
    {
    }
}

#[AsAttributeType(attributeType: 'dummy', label: 'dummy', formType: 'DummyType', configurationFormType: 'DummyAttributeConfigurationType')]
class DummyAttributeTypeWithConfigurationFormType implements AttributeTypeInterface
{
    public function getStorageType(): string
    {
        return 'default';
    }

    public function getType(): string
    {
        return 'dummy';
    }

    public function validate(AttributeValueInterface $attributeValue, ExecutionContextInterface $context, array $configuration): void
    {
    }
}
