<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class PaymentMethodTypeExtension extends AbstractTypeExtension
{
    /**
     * @var string
     */
    private $encryptingAlgorithm;

    /**
     * @var string
     */
    private $encryptingInitializationVector;

    /**
     * @var string
     */
    private $encryptingSecret;

    /**
     * @param string $encryptingAlgorithm
     * @param string $encryptingInitializationVector
     * @param string $encryptingSecret
     */
    public function __construct($encryptingAlgorithm, $encryptingInitializationVector, $encryptingSecret)
    {
        $this->encryptingAlgorithm = $encryptingAlgorithm;
        $this->encryptingInitializationVector = $encryptingInitializationVector;
        $this->encryptingSecret = $encryptingSecret;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
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
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $paymentMethod = $event->getData();

                if (!$paymentMethod instanceof PaymentMethodInterface) {
                    return;
                }

                $gatewayConfig = $paymentMethod->getGatewayConfig();
                $gatewayConfig->setConfig(array_map(function ($configuration) {
                    $decryptedConfigurationValue = openssl_decrypt(
                        base64_decode($configuration),
                        $this->encryptingAlgorithm,
                        $this->encryptingSecret,
                        OPENSSL_RAW_DATA,
                        $this->encryptingInitializationVector
                    );

                    if (false === $decryptedConfigurationValue) {
                        throw new \RuntimeException(sprintf('Encrypting failed for value "%s"', $configuration));
                    }

                    return trim($decryptedConfigurationValue);
                }, $gatewayConfig->getConfig()));
            })
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $paymentMethod = $event->getData();

                if (!$paymentMethod instanceof PaymentMethodInterface) {
                    return;
                }

                $gatewayConfig = $paymentMethod->getGatewayConfig();
                if (null === $gatewayConfig->getGatewayName()) {
                    $gatewayConfig->setGatewayName(StringInflector::nameToLowercaseCode($paymentMethod->getName()));
                }

                $gatewayConfig->setConfig(array_map(function ($configuration) {
                    $encryptedConfigurationValue = base64_encode(openssl_encrypt(
                        $configuration,
                        $this->encryptingAlgorithm,
                        $this->encryptingSecret,
                        OPENSSL_RAW_DATA,
                        $this->encryptingInitializationVector
                    ));

                    if (false === $encryptedConfigurationValue) {
                        throw new \RuntimeException(sprintf('Encrypting failed for value "%s"', $configuration));
                    }

                    return trim($encryptedConfigurationValue);
                }, $gatewayConfig->getConfig()));
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return PaymentMethodType::class;
    }
}
