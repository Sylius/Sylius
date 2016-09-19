<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Form event listener that builds variant form dynamically based on data.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class BuildVariantFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
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
        return [FormEvents::PRE_SET_DATA => 'preSetData'];
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
        $variable = $variant->getProduct();

        // If the object has options, lets add this configuration field.
        if ($variable->hasOptions()) {
            $form->add($this->factory->createNamed('options', 'sylius_product_option_value_collection', $variant->getOptions(), [
                'options' => $variable->getOptions(),
                'auto_initialize' => false,
            ]));
        }
    }
}
