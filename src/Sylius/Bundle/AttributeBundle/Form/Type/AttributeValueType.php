<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AttributeBundle\Form\Type;

use Sylius\Bundle\LocaleBundle\Form\DataTransformer\LocaleToCodeTransformer;
use Sylius\Bundle\LocaleBundle\Form\Type\LocaleChoiceType;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ReversedTransformer;

abstract class AttributeValueType extends AbstractResourceType
{
    /**
     * @param DataTransformerInterface<LocaleInterface, string|null>|null $localeToCodeTransformer
     */
    public function __construct(
        string $dataClass,
        array $validationGroups,
        protected string $attributeChoiceType,
        protected RepositoryInterface $attributeRepository,
        protected RepositoryInterface $localeRepository,
        protected FormTypeRegistryInterface $formTypeRegistry,
        protected ?DataTransformerInterface $localeToCodeTransformer = null,
    ) {
        if (null === $this->localeToCodeTransformer) {
            trigger_deprecation(
                'sylius/attribute-bundle',
                '1.13',
                'Not passing a "%s" instance as argument 7 to "%s" is deprecated. It will not be possible in Sylius 2.0.',
                LocaleToCodeTransformer::class,
                self::class,
            );
        }

        parent::__construct($dataClass, $validationGroups);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
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

                $localeCode = $attributeValue->getLocaleCode();

                $this->addValueField($event->getForm(), $attribute, $localeCode);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $attributeValue = $event->getData();
                $localeCode = $attributeValue['localeCode'];

                if (!isset($attributeValue['attribute'])) {
                    return;
                }

                $attribute = $this->attributeRepository->findOneBy(['code' => $attributeValue['attribute']]);
                if (!$attribute instanceof AttributeInterface) {
                    return;
                }

                $this->addValueField($event->getForm(), $attribute, $localeCode);
            })
        ;

        $builder->get('localeCode')->addModelTransformer(
            new ReversedTransformer($this->localeToCodeTransformer ?? new ResourceToIdentifierTransformer($this->localeRepository, 'code')),
        );
    }

    protected function addValueField(
        FormInterface $form,
        AttributeInterface $attribute,
        ?string $localeCode = null,
    ): void {
        $form->add('value', $this->formTypeRegistry->get($attribute->getType(), 'default'), [
            'auto_initialize' => false,
            'configuration' => $attribute->getConfiguration(),
            'label' => $attribute->getName(),
            'locale_code' => $localeCode,
        ]);
    }
}
