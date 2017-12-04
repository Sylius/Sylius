<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AttributeBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

abstract class AttributeType extends AbstractResourceType
{
    /**
     * @var string
     */
    protected $attributeTranslationType;

    /**
     * @var FormTypeRegistryInterface
     */
    protected $formTypeRegistry;

    /**
     * {@inheritdoc}
     *
     * @param string $attributeTranslationType
     * @param FormTypeRegistryInterface $formTypeRegistry
     */
    public function __construct(
        string $dataClass,
        array $validationGroups,
        string $attributeTranslationType,
        FormTypeRegistryInterface $formTypeRegistry
    ) {
        parent::__construct($dataClass, $validationGroups);

        $this->attributeTranslationType = $attributeTranslationType;
        $this->formTypeRegistry = $formTypeRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => $this->attributeTranslationType,
                'label' => 'sylius.form.attribute.translations',
            ])
            ->add('type', AttributeTypeChoiceType::class, [
                'label' => 'sylius.form.attribute.type',
                'disabled' => true,
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $attribute = $event->getData();

            if (!$attribute instanceof AttributeInterface) {
                return;
            }

            if (!$this->formTypeRegistry->has($attribute->getType(), 'configuration')) {
                return;
            }

            $event->getForm()->add('configuration', $this->formTypeRegistry->get($attribute->getType(), 'configuration'), [
                'auto_initialize' => false,
                'label' => 'sylius.form.attribute_type.configuration',
            ]);
        });
    }
}
