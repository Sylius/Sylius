<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Extension;

use Sylius\Bundle\CoreBundle\Form\Type\Product\ChannelPricingType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingCategoryChoiceType;
use Sylius\Bundle\TaxationBundle\Form\Type\TaxCategoryChoiceType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ProductVariantTypeExtension extends AbstractTypeExtension
{
    /**
     * @var EventSubscriberInterface
     */
    private $channelPricingFormSubscriber;

    /**
     * {@inheritdoc}
     *
     * @param EventSubscriberInterface $channelPricingFormSubscriber
     */
    public function __construct(EventSubscriberInterface $channelPricingFormSubscriber)
    {
        $this->channelPricingFormSubscriber = $channelPricingFormSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('version', HiddenType::class)
            ->add('tracked', CheckboxType::class, [
                'label' => 'sylius.form.variant.tracked',
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
            ->add('channelPricings', CollectionType::class, [
                'entry_type' => ChannelPricingType::class,
                'label' => 'sylius.form.variant.price',
                'allow_add' => false,
                'allow_delete' => false,
                'error_bubbling' => false,
            ])
            ->addEventSubscriber($this->channelPricingFormSubscriber)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return ProductVariantType::class;
    }
}
