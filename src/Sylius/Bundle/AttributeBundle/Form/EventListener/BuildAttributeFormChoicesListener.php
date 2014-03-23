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

use Sylius\Component\Attribute\Model\AttributeTypes;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Form event listener that builds choices for attribute form.
 *
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class BuildattributeFormChoicesListener implements EventSubscriberInterface
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
        return array(FormEvents::PRE_SET_DATA => 'buildChoices');
    }

    /**
     * Builds choices for attribute form.
     *
     * @param FormEvent $event
     */
    public function buildChoices(FormEvent $event)
    {
        $attribute = $event->getData();
        if (null === $attribute) {
            return;
        }

        $type = $attribute->getType();

        if (null === $type || AttributeTypes::CHOICE === $type) {
            $event->getForm()->add(
                $this->factory->createNamed('choices', 'collection', null, array(
                    'type'         => 'text',
                    'allow_add'    => true,
                    'allow_delete' => true,
                    'by_reference' => false
                ))
            );
        }
    }
}
