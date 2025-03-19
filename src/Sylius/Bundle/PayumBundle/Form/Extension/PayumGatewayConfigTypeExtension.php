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

namespace Sylius\Bundle\PayumBundle\Form\Extension;

use Payum\Core\Payum;
use Sylius\Bundle\PaymentBundle\CommandProvider\ServiceProviderAwareCommandProviderInterface;
use Sylius\Bundle\PaymentBundle\Form\Type\GatewayConfigType;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Webmozart\Assert\Assert;

final class PayumGatewayConfigTypeExtension extends AbstractTypeExtension
{
    public function __construct(
        private Payum $payum,
        private ServiceProviderAwareCommandProviderInterface $gatewayFactoryCommandProvider,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $gatewayConfig = $event->getData();

                if (!$gatewayConfig instanceof GatewayConfigInterface) {
                    return;
                }

                $factoryName = $gatewayConfig->getFactoryName();
                Assert::notNull($factoryName, 'A factory name is required.');

                // Check if a Payum factory exists
                $supportPayum = isset($this->payum->getGatewayFactories()[$factoryName]);

                if (!$supportPayum) {
                    $gatewayConfig->setUsePayum(false);
                }

                // Check if PaymentRequest exists
                $supportPaymentRequest = null !== $this->gatewayFactoryCommandProvider->getCommandProvider($factoryName);

                $event->getForm()->add('usePayum', CheckboxType::class, [
                    'required' => false,
                    'label' => 'sylius.form.gateway_config.use_payum',
                    'disabled' => !($supportPayum && $supportPaymentRequest) || null !== $gatewayConfig->getId(),
                ]);

                $event->setData($gatewayConfig);
            })
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [GatewayConfigType::class];
    }
}
