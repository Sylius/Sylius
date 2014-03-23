<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Form event listener that builds product property form dynamically based on product data.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class BuildAttributeValueFormListener implements EventSubscriberInterface
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
        return array(FormEvents::PRE_SET_DATA => 'buildForm');
    }

    /**
     * Builds proper product form after setting the product.
     *
     * @param FormEvent $event
     */
    public function buildForm(FormEvent $event)
    {
        $attributeValue = $event->getData();
        $form = $event->getForm();

        if (null === $attributeValue) {
            $form->add($this->factory->createNamed('value', 'text', null, array('auto_initialize' => false)));

            return;
        }

        $options = array('label' => $attributeValue->getName(), 'auto_initialize' => false);

        if (is_array($attributeValue->getConfiguration())) {
            $options = array_merge($options, $attributeValue->getConfiguration());
        }

        // If we're editing the attribute value, let's just render the value field, not full selection.
        $form
            ->remove('attribute')
            ->add($this->factory->createNamed('value', $attributeValue->getType(), null, $options))
        ;
    }
}
