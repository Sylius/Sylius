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
use Symfony\Component\DependencyInjection\Definition;

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

    protected function getContainerExtensions(): array
    {
        return [new SyliusPaymentExtension()];
    }
}
