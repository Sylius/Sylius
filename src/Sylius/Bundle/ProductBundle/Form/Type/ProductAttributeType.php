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

namespace Sylius\Bundle\ProductBundle\Form\Type;

use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;

final class ProductAttributeType extends AttributeType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('position', IntegerType::class, [
                'required' => false,
                'label' => 'sylius.form.product_attribute.position',
                'invalid_message' => 'sylius.product_attribute.invalid',
            ])
        ;
    }

    public function addSelectOptions (FormEvent $event)
    {
        $attribute = $event->getData();

        if (!$attribute instanceof ProductAttributeInterface) {
            return;
        }

        if (!$this->formTypeRegistry->has($attribute->getType(), 'configuration')) {
            return;
        }

        $event->getForm()->add('configuration', $this->formTypeRegistry->get($attribute->getType(), 'configuration'), [
            'auto_initialize' => false,
            'label' => 'sylius.form.attribute_type.configuration',
        ]);

        if($attribute->getType() == SelectAttributeType::TYPE)
        {
            $event->getForm()->add('selectOptions', CollectionType::class, [
                'entry_type' => ProductAttributeSelectOptionType::class,
                'label' => 'sylius.form.attribute.select_option_values',
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_product_attribute';
    }
}
