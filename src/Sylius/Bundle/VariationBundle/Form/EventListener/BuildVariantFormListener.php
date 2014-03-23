<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariationBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Form event listener that builds variant form dynamically based on data.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class BuildVariantFormListener implements EventSubscriberInterface
{
    /**
     * Variable object name.
     *
     * @var string
     */
    protected $variableName;

    /**
     * Form factory.
     *
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * Constructor.
     *
     * @param string               $variableName
     * @param FormFactoryInterface $factory
     */
    public function __construct($variableName, FormFactoryInterface $factory)
    {
        $this->variableName = $variableName;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    /**
     * Builds proper variant form after setting the product.
     *
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $variant = $event->getData();
        $form = $event->getForm();

        if (null === $variant) {
            return;
        }

        // Get related variable object.
        $variable = $variant->getObject();

        // If the object has options, lets add this configuration field.
        if ($variable->hasOptions()) {
            $form->add($this->factory->createNamed('options', sprintf('sylius_%s_option_value_collection', $this->variableName), $variant->getOptions(), array(
                'options'         => $variable->getOptions(),
                'auto_initialize' => false
            )));
        }
    }
}
