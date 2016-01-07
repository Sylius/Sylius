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
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class BuildAttributeValueFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var string
     */
    protected $subjectName;

    /**
     * @var RepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @param FormFactoryInterface $formFactory
     * @param string $subjectName
     * @param RepositoryInterface $attributeRepository
     */
    public function __construct(FormFactoryInterface $formFactory, $subjectName, RepositoryInterface $attributeRepository)
    {
        $this->formFactory = $formFactory;
        $this->subjectName = $subjectName;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preSubmit',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $attributeValue = $event->getData();

        if (null === $attributeValue || null === $attributeValue->getAttribute()) {
            throw new \InvalidArgumentException(sprintf(
                'Cannot create an attribute value form without passing instance of "%s" with attribute defined as data.',
                AttributeValueInterface::class
            ));
        }

        $form = $event->getForm();

        $this->addValueField($form, $attributeValue->getAttribute(), $attributeValue->getValue());
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $attributeValue = $event->getData();

        if (!isset($attributeValue['value']) || !isset($attributeValue['attribute'])) {
            throw new \InvalidArgumentException('Cannot create an attribute value form on pre submit event without "attribute" and "value" keys in data.');
        }

        $form = $event->getForm();
        $attribute = $this->attributeRepository->find($attributeValue['attribute']);

        $this->addValueField($form, $attribute);

    }

    /**
     * @param FormInterface $form
     * @param AttributeInterface $attribute
     */
    private function addValueField(FormInterface $form, AttributeInterface $attribute, $data = null)
    {
        $type = $attribute->getType();

        $options = array('auto_initialize' => false, 'label' => $attribute->getName());

        $form
            ->add($this->formFactory->createNamed('value', 'sylius_attribute_type_'.$type, $data, $options))
        ;
    }
}
