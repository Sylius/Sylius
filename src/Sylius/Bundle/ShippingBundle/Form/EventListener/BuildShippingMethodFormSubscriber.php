<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Form\EventListener;

use Sylius\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @internal
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class BuildShippingMethodFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $calculatorRegistry;

    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * @var FormTypeRegistryInterface
     */
    private $formTypeRegistry;

    /**
     * @param ServiceRegistryInterface $calculatorRegistry
     * @param FormFactoryInterface $factory
     * @param FormTypeRegistryInterface $formTypeRegistry
     */
    public function __construct(
        ServiceRegistryInterface $calculatorRegistry,
        FormFactoryInterface $factory,
        FormTypeRegistryInterface $formTypeRegistry
    ) {
        $this->calculatorRegistry = $calculatorRegistry;
        $this->factory = $factory;
        $this->formTypeRegistry = $formTypeRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $method = $event->getData();

        if (null === $method || null === $method->getId()) {
            return;
        }

        $this->addConfigurationFields($event->getForm(), $method->getCalculator(), $method->getConfiguration());
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (empty($data) || !array_key_exists('calculator', $data)) {
            return;
        }

        $this->addConfigurationFields($event->getForm(), $data['calculator']);
    }

    /**
     * @param FormInterface $form
     * @param string $calculatorName
     * @param array $data
     */
    protected function addConfigurationFields(FormInterface $form, $calculatorName, array $data = [])
    {
        /** @var CalculatorInterface $calculator */
        $calculator = $this->calculatorRegistry->get($calculatorName);

        $calculatorType = $calculator->getType();
        if (!$this->formTypeRegistry->has($calculatorType, 'default')) {
            return;
        }

        $configurationField = $this->factory->createNamed(
            'configuration',
            $this->formTypeRegistry->get($calculatorType, 'default'),
            $data,
            ['auto_initialize' => false]
        );

        $form->add($configurationField);
    }
}
