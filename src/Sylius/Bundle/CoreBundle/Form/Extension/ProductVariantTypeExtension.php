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

namespace Sylius\Bundle\CoreBundle\Form\Extension;

use Sylius\Bundle\CoreBundle\Form\Type\ChannelCollectionType;
use Sylius\Bundle\CoreBundle\Form\Type\Product\ChannelPricingType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingCategoryChoiceType;
use Sylius\Bundle\TaxationBundle\Form\Type\TaxCategoryChoiceType;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class ProductVariantTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('version', HiddenType::class)
            ->add('tracked', CheckboxType::class, [
                'label' => 'sylius.form.variant.tracked',
                'help' => 'sylius.form.variant.tracked_help',
            ])
            ->add('orderItemUnitGenerationMode', ChoiceType::class, [
                'label' => 'sylius.form.variant.order_item_unit_generation_mode',
                'help' => 'sylius.form.variant.order_item_unit_generation_mode_help',
                'choices' => [
                    'sylius.form.variant.order_item_unit_generation_mode.multiple' => ProductVariantInterface::ORDER_ITEM_UNIT_GENERATION_MODE_MULTIPLE,
                    'sylius.form.variant.order_item_unit_generation_mode.single' => ProductVariantInterface::ORDER_ITEM_UNIT_GENERATION_MODE_SINGLE,
                ],
            ])
            ->add('shippingRequired', CheckboxType::class, [
                'label' => 'sylius.form.variant.shipping_required',
            ])
            ->add('onHand', IntegerType::class, [
                'label' => 'sylius.form.variant.on_hand',
            ])
            ->add('width', NumberType::class, [
                'required' => false,
                'label' => 'sylius.form.variant.width',
                'invalid_message' => 'sylius.product_variant.width.invalid',
            ])
            ->add('height', NumberType::class, [
                'required' => false,
                'label' => 'sylius.form.variant.height',
                'invalid_message' => 'sylius.product_variant.height.invalid',
            ])
            ->add('depth', NumberType::class, [
                'required' => false,
                'label' => 'sylius.form.variant.depth',
                'invalid_message' => 'sylius.product_variant.depth.invalid',
            ])
            ->add('weight', NumberType::class, [
                'required' => false,
                'label' => 'sylius.form.variant.weight',
                'invalid_message' => 'sylius.product_variant.weight.invalid',
            ])
            ->add('taxCategory', TaxCategoryChoiceType::class, [
                'required' => false,
                'placeholder' => '---',
                'label' => 'sylius.form.product_variant.tax_category',
            ])
            ->add('shippingCategory', ShippingCategoryChoiceType::class, [
                'required' => false,
                'placeholder' => 'sylius.ui.no_requirement',
                'label' => 'sylius.form.product_variant.shipping_category',
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $productVariant = $event->getData();

            $event->getForm()->add('channelPricings', ChannelCollectionType::class, [
                'entry_type' => ChannelPricingType::class,
                'entry_options' => fn (ChannelInterface $channel) => [
                    'channel' => $channel,
                    'product_variant' => $productVariant,
                    'required' => false,
                ],
                'label' => 'sylius.form.variant.price',
            ]);
        });
    }

    public function getExtendedType(): string
    {
        return ProductVariantType::class;
    }

    public static function getExtendedTypes(): iterable
    {
        return [ProductVariantType::class];
    }
}
