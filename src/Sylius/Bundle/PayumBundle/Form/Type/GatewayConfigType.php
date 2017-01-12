<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PayumBundle\Form\Type;

use Payum\Core\Model\GatewayConfigInterface;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class GatewayConfigType extends AbstractResourceType
{
    /**
     * @var ServiceRegistryInterface
     */
    private $gatewayConfigurationTypeRegistry;

    /**
     * {@inheritdoc}
     *
     * @param ServiceRegistryInterface $gatewayConfigurationTypeRegistry
     */
    public function __construct(
        $dataClass,
        $validationGroups = [],
        ServiceRegistryInterface $gatewayConfigurationTypeRegistry
    ) {
        parent::__construct($dataClass, $validationGroups);

        $this->gatewayConfigurationTypeRegistry = $gatewayConfigurationTypeRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('gatewayName', TextType::class, [
                'label' => 'sylius.form.gateway_config.gateway_name',
            ])
            ->add('factoryName', TextType::class, [
                'label' => 'sylius.form.gateway_config.factory_name',
                'disabled' => true,
                'data' => $options['factory'],
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $gatewayConfig = $event->getData();

            if (!$gatewayConfig instanceof GatewayConfigInterface) {
                return;
            }

            if (!$this->gatewayConfigurationTypeRegistry->has($options['factory'], 'configuration')) {
                return;
            }

            $event->getForm()->add('config', get_class($this->gatewayConfigurationTypeRegistry->get($options['factory'])), [
                'label' => false,
                'auto_initialize' => false,
            ]);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('factory', 'stripe_checkout');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_payum_gateway_config';
    }
}
