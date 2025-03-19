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

namespace Sylius\Bundle\PaymentBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\PaymentBundle\Attribute\AsGatewayConfigurationType;
use Sylius\Bundle\PaymentBundle\Attribute\AsNotifyPaymentProvider;
use Sylius\Bundle\PaymentBundle\Attribute\AsPaymentMethodsResolver;
use Sylius\Bundle\PaymentBundle\DependencyInjection\SyliusPaymentExtension;
use Sylius\Bundle\PaymentBundle\Tests\Stub\GatewayConfigurationTypeStub;
use Sylius\Bundle\PaymentBundle\Tests\Stub\NotifyPaymentProviderStub;
use Sylius\Bundle\PaymentBundle\Tests\Stub\PaymentMethodsResolverStub;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class SyliusPaymentExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_autoconfigures_payment_methods_resolver_with_attribute(): void
    {
        $this->container->setDefinition(
            'acme.payment_methods_resolver_with_attribute',
            (new Definition())
                ->setClass(PaymentMethodsResolverStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.payment_methods_resolver_with_attribute',
            AsPaymentMethodsResolver::SERVICE_TAG,
            ['type' => 'test', 'label' => 'Test', 'priority' => 15],
        );
    }

    /** @test */
    public function it_autoconfigures_gateway_configuration_type_with_attribute(): void
    {
        $this->container->setDefinition(
            'acme.gateway_configuration_type_with_attribute',
            (new Definition())
                ->setClass(GatewayConfigurationTypeStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.gateway_configuration_type_with_attribute',
            AsGatewayConfigurationType::SERVICE_TAG,
            ['type' => 'test', 'label' => 'Test', 'priority' => 15],
        );
    }

    /** @test */
    public function it_autoconfigures_payment_notify_provider_with_attribute(): void
    {
        $this->container->setDefinition(
            'acme.payment_notify_provider_with_attribute',
            (new Definition())
                ->setClass(NotifyPaymentProviderStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.payment_notify_provider_with_attribute',
            AsNotifyPaymentProvider::SERVICE_TAG,
            ['priority' => 15],
        );
    }

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

        $this->assertContainerBuilderHasParameter(
            'sylius.gateway_config.validation_groups',
            ['paypal_express_checkout' => ['sylius', 'paypal'], 'offline' => ['sylius']],
        );
    }

    /** @test */
    public function it_loads_parameter_with_payment_request_states_that_should_be_cancelled_when_payment_method_is_changed(): void
    {
        $this->load([
            'payment_request' => [
                'states_to_be_cancelled_when_payment_method_changed' => [
                    PaymentRequestInterface::STATE_NEW,
                    PaymentRequestInterface::STATE_PROCESSING,
                ],
            ],
        ]);

        $this->assertContainerBuilderHasParameter(
            'sylius.payment_request.states_to_be_cancelled_when_payment_method_changed',
            [PaymentRequestInterface::STATE_NEW, PaymentRequestInterface::STATE_PROCESSING],
        );
    }

    /** @test */
    public function it_loads_encryption_services_when_encryption_is_enabled(): void
    {
        $this->load([
            'encryption' => [
                'enabled' => true,
            ],
        ]);

        $this->assertContainerBuilderHasParameter('sylius.encryption.enabled', true);
        $this->assertContainerBuilderHasParameter('sylius.encryption.disabled_for_factories', []);

        $this->compile();

        $this->assertContainerBuilderHasService('sylius.encrypter');
    }

    /** @test */
    public function it_populates_encryption_disabled_for_factories_parameter(): void
    {
        $this->load([
            'encryption' => [
                'disabled_for_factories' => ['paypal_express_checkout'],
            ],
        ]);

        $this->assertContainerBuilderHasParameter('sylius.encryption.disabled_for_factories', ['paypal_express_checkout']);
    }

    /** @test */
    public function it_does_not_load_encryption_services_when_encryption_is_disabled(): void
    {
        $this->load([
            'encryption' => [
                'enabled' => false,
            ],
        ]);

        $this->assertContainerBuilderHasParameter('sylius.encryption.enabled', false);

        $this->compile();

        $this->assertContainerBuilderNotHasService('sylius.encrypter');
    }

    /**
     * @test
     *
     * @dataProvider getCommandProviderLoader
     */
    public function it_allows_adding_a_gateway_factory_command_provider_using_yaml(string $loaderClass, string $serviceFileDefinition): void
    {
        $this->load();

        $fileLocator = new FileLocator(__DIR__ . '/../Resources/config');
        $loader = new $loaderClass($this->container, $fileLocator);
        $loader->load($serviceFileDefinition);

        $this->compile();

        /** @var ServiceLocatorArgument $serviceLocatorArgument */
        $serviceLocatorArgument = $this->container->getDefinition('sylius.command_provider.gateway_factory')->getArgument(2);

        $tag = $serviceLocatorArgument->getTaggedIteratorArgument()->getTag();
        $indexAttribute = $serviceLocatorArgument->getTaggedIteratorArgument()->getIndexAttribute();

        $foundServices = [];
        foreach ($this->container->findTaggedServiceIds($tag) as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                $this->assertArrayHasKey($indexAttribute, $attributes);
                $foundServices[$serviceId] = $attributes;
            }
        }

        $this->assertArrayHasKey('acme.sylius_example.command_provider.sylius_payment', $foundServices);
    }

    /**
     * @test
     *
     * @dataProvider getHttpResponseProviderLoader
     */
    public function it_allows_adding_a_gateway_factory_http_response_provider_using_yaml(string $loaderClass, string $serviceFileDefinition): void
    {
        $this->load();

        $fileLocator = new FileLocator(__DIR__ . '/../Resources/config');
        $loader = new $loaderClass($this->container, $fileLocator);
        $loader->load($serviceFileDefinition);

        $this->compile();

        /** @var ServiceLocatorArgument $serviceLocatorArgument */
        $serviceLocatorArgument = $this->container->getDefinition('sylius.provider.payment_request.http_response.gateway_factory')->getArgument(1);

        $tag = $serviceLocatorArgument->getTaggedIteratorArgument()->getTag();
        $indexAttribute = $serviceLocatorArgument->getTaggedIteratorArgument()->getIndexAttribute();

        $foundServices = [];
        foreach ($this->container->findTaggedServiceIds($tag) as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                $this->assertArrayHasKey($indexAttribute, $attributes);
                $foundServices[$serviceId] = $attributes;
            }
        }

        $this->assertArrayHasKey('acme.sylius_example.http_response_provider.sylius_payment', $foundServices);
    }

    public static function getCommandProviderLoader(): iterable
    {
        yield 'Load YAML' => [
            YamlFileLoader::class,
            'command_provider.yaml',
        ];
        yield 'Load XML' => [
            XmlFileLoader::class,
            'command_provider.xml',
        ];
    }

    public static function getHttpResponseProviderLoader(): iterable
    {
        yield 'Load YAML' => [
            YamlFileLoader::class,
            'http_response_provider.yaml',
        ];
        yield 'Load XML' => [
            XmlFileLoader::class,
            'http_response_provider.xml',
        ];
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusPaymentExtension()];
    }
}
