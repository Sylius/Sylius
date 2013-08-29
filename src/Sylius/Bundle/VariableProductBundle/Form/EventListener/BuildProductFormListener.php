<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariableProductBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * If product has variants, we should not be able to change its options.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class BuildProductFormListener implements EventSubscriberInterface
{
    /**
     * Form factory.
     *
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * Constructor.
     *
     * @param FormFactoryInterface $factory
     */
    public function __construct(FormFactoryInterface $factory)
    {
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
     * Removes some of the fields when editing the product.
     *
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $product = $event->getData();
        $form = $event->getForm();

        if (null === $product) {
            return;
        }

        if (!$product->hasVariants()) {
            $optionsField = $this->factory->createNamed('options', 'sylius_option_choice', $product->getOptions(), array(
                'required'        => false,
                'multiple'        => true,
                'label'           => 'sylius.form.product.options',
                'auto_initialize' => false,
            ));

            $form->add($optionsField);
        }
    }
}
