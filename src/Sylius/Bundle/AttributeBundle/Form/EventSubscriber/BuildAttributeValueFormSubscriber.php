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

use Sylius\Bundle\AttributeBundle\Factory\AttributeValueTypeConfigurationFactoryInterface;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class BuildAttributeValueFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var string
     */
    protected $subjectName;

    /**
     * @var AttributeValueTypeConfigurationFactoryInterface
     */
    protected $attributeValueTypeConfigurationFactory;

    /**
     * @param RepositoryInterface $attributeRepository
     * @param string $subjectName
     * @param AttributeValueTypeConfigurationFactoryInterface $attributeValueTypeConfigurationFactory
     */
    public function __construct(
        RepositoryInterface $attributeRepository,
        $subjectName,
        AttributeValueTypeConfigurationFactoryInterface $attributeValueTypeConfigurationFactory
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->subjectName = $subjectName;
        $this->attributeValueTypeConfigurationFactory = $attributeValueTypeConfigurationFactory;
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
        $attributeValue = $event->getData();

        if (null === $attributeValue || null === $attributeValue->getAttribute()) {
            return;
        }

        $this->addValueField($event->getForm(), $attributeValue->getAttribute());
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $attributeValue = $event->getData();

        if (!isset($attributeValue['attribute'])) {
            throw new \InvalidArgumentException('Cannot create an attribute value form on pre submit event without an "attribute" key in data.');
        }

        $form = $event->getForm();
        $attribute = $this->attributeRepository->find($attributeValue['attribute']);

        $this->addValueField($form, $attribute);
    }

    /**
     * @param FormInterface $form
     * @param AttributeInterface $attribute
     */
    private function addValueField(FormInterface $form, AttributeInterface $attribute)
    {
        $options = ['auto_initialize' => false, 'label' => $attribute->getName()];

        $attributeValueConfig = $this->attributeValueTypeConfigurationFactory
            ->create($attribute, $this->subjectName);

        $form->add(
            $attributeValueConfig->getName(),
            $attributeValueConfig->getType(),
            array_merge($options, $attributeValueConfig->getFormOptions())
        );
    }
}
