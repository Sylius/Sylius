<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\Form\EventSubscriber;

use Sylius\Component\Attribute\Model\AttributeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class BuildAttributeFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    protected $factory;

    /**
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->factory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'addConfigurationFields',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function addConfigurationFields(FormEvent $event)
    {
        $attribute = $event->getData();
        $form = $event->getForm();

        $this->addRequiredFields($attribute, $form, 'configuration');
        $this->addRequiredFields($attribute, $form, 'validation');
    }

    /**
     * @param AttributeInterface $attribute
     * @param FormInterface $form
     * @param string $fieldsType
     */
    private function addRequiredFields(AttributeInterface $attribute, FormInterface $form, $fieldsType)
    {
        try {
            $requiredFields = $this->factory->createNamed(
                $fieldsType,
                'sylius_attribute_type_'.$fieldsType.'_'.$attribute->getType(),
                null,
                array(
                    'auto_initialize' => false,
                    'label' => 'sylius.attribute_type.'.$fieldsType,
                )
            );

            $form->add($requiredFields);
        } catch (InvalidArgumentException $exception) {
        }
    }
}
