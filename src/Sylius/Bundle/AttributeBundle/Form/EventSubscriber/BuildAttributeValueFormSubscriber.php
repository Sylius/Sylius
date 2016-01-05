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

use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

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
        $form = $event->getForm();
        $options = array('label' => false, 'auto_initialize' => false);

        if (null === $attributeValue) {
            $form->add($this->formFactory->createNamed('value', 'sylius_attribute_type_text', null, $options));

            return;
        }

        $attribute = $attributeValue->getAttribute();
        $options['label'] = $attribute->getName();

        $form
            ->add($this->formFactory->createNamed('value', 'sylius_attribute_type_'.$attribute->getType(), $attributeValue->getValue(), $options))
        ;
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $attributeValue = $event->getData();
        $form = $event->getForm();

        if (empty($attributeValue) || !isset($attributeValue['value'])) {
            return;
        }

        $attribute = $this->attributeRepository->find($attributeValue['attribute']);
        $type = $attribute->getType();
        $storageType = $attribute->getStorageType();

        $options = array('auto_initialize' => false);

        $form
            ->add($this->formFactory->createNamed('value', 'sylius_attribute_type_'.$type, $this->provideAttributeValue($storageType, $attributeValue['value']), $options))
        ;
    }

    /**
     * @param string $storageType
     * @param mixed $value
     *
     * @return mixed
     */
    private function provideAttributeValue($storageType, $value)
    {
        if (AttributeValueInterface::STORAGE_DATE === $storageType) {
            $dateValue = sprintf('%d/%d/%d',
                $value['year'],
                $value['month'],
                $value['day']
            );

            return new \DateTime($dateValue);
        }

        if (AttributeValueInterface::STORAGE_DATETIME === $storageType) {
            $dateTimeValue = sprintf('%d/%d/%d %d:%d',
                $value['date']['year'],
                $value['date']['month'],
                $value['date']['day'],
                $value['time']['hour'],
                $value['time']['minute']
            );

            return new \DateTime($dateTimeValue);
        }

        if (AttributeValueInterface::STORAGE_BOOLEAN === $storageType) {
            return ('1' === $value) ? true : false;
        }

        return $value;
    }
}
