<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\Form\Type;

use Sylius\Bundle\LocaleBundle\Form\Type\LocaleChoiceType;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ReversedTransformer;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
abstract class AttributeValueType extends AbstractResourceType
{
    /**
     * @var string
     */
    protected $attributeChoiceType;

    /**
     * @var RepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var RepositoryInterface
     */
    protected $localeRepository;

    /**
     * @var FormTypeRegistryInterface
     */
    protected $formTypeRegistry;

    /**
     * @param string $dataClass
     * @param array $validationGroups
     * @param string $attributeChoiceType
     * @param RepositoryInterface $attributeRepository
     * @param RepositoryInterface $localeRepository
     * @param FormTypeRegistryInterface $formTypeTypeRegistry
     */
    public function __construct(
        $dataClass,
        array $validationGroups,
        $attributeChoiceType,
        RepositoryInterface $attributeRepository,
        RepositoryInterface $localeRepository,
        FormTypeRegistryInterface $formTypeTypeRegistry
    ) {
        parent::__construct($dataClass, $validationGroups);

        $this->attributeChoiceType = $attributeChoiceType;
        $this->attributeRepository = $attributeRepository;
        $this->localeRepository = $localeRepository;
        $this->formTypeRegistry = $formTypeTypeRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('localeCode', LocaleChoiceType::class)
            ->add('attribute', $this->attributeChoiceType)
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $attributeValue = $event->getData();

                if (!$attributeValue instanceof AttributeValueInterface) {
                    return;
                }

                $attribute = $attributeValue->getAttribute();
                if (null === $attribute) {
                    return;
                }

                $this->addValueField($event->getForm(), $attribute);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $attributeValue = $event->getData();

                if (!isset($attributeValue['attribute'])) {
                    return;
                }

                $attribute = $this->attributeRepository->findOneBy(['code' => $attributeValue['attribute']]);
                if (!$attribute instanceof AttributeInterface) {
                    return;
                }

                $this->addValueField($event->getForm(), $attribute);
            })
        ;

        $builder->get('localeCode')->addModelTransformer(
            new ReversedTransformer(new ResourceToIdentifierTransformer($this->localeRepository, 'code'))
        );
    }

    /**
     * @param FormInterface $form
     * @param AttributeInterface $attribute
     */
    protected function addValueField(FormInterface $form, AttributeInterface $attribute)
    {
        $form->add('value', $this->formTypeRegistry->get($attribute->getType(), 'default'), [
            'auto_initialize' => false,
            'configuration' => $attribute->getConfiguration(),
            'label' => $attribute->getName(),
        ]);
    }
}
