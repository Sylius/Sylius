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
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 * @author Liverbool <liverbool@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class BuildAttributeFormChoicesListener implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * @var ServiceRegistryInterface
     */
    protected $attributeTypeRegistry;

    /**
     * @param FormFactoryInterface     $factory
     * @param ServiceRegistryInterface $attributeTypeRegistry
     */
    public function __construct(FormFactoryInterface $factory, ServiceRegistryInterface $attributeTypeRegistry)
    {
        $this->factory = $factory;
        $this->attributeTypeRegistry = $attributeTypeRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'setAttributeStorageType',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function setAttributeStorageType(FormEvent $event)
    {
        $attribute = $event->getData();

        $attributeType = $this->attributeTypeRegistry->get($attribute->getType());
        $attribute->setStorageType($attributeType->getStorageType());
    }
}
