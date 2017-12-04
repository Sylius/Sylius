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

namespace Sylius\Bundle\CoreBundle\Form\Extension;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\PaymentBundle\Form\Type\PaymentMethodType;
use Sylius\Bundle\PayumBundle\Form\Type\GatewayConfigType;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class PaymentMethodTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $gatewayFactory = $options['data']->getGatewayConfig();

        $builder
            ->add('channels', ChannelChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'label' => 'sylius.form.payment_method.channels',
            ])
            ->add('gatewayConfig', GatewayConfigType::class, [
                'label' => false,
                'data' => $gatewayFactory,
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event): void {
                $paymentMethod = $event->getData();

                if (!$paymentMethod instanceof PaymentMethodInterface) {
                    return;
                }

                $gatewayConfig = $paymentMethod->getGatewayConfig();
                if (null === $gatewayConfig->getGatewayName() && null !== $paymentMethod->getCode()) {
                    $gatewayConfig->setGatewayName(StringInflector::nameToLowercaseCode($paymentMethod->getCode()));
                }
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType(): string
    {
        return PaymentMethodType::class;
    }
}
