<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Product;

use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType as BaseProductVariantType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProductVariantType extends BaseProductVariantType
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
    public function __construct($dataClass, array $validationGroups = [], EventSubscriberInterface $channelPricingFormSubscriber)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->channelPricingFormSubscriber = $channelPricingFormSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('tracked', 'checkbox', [
                'label' => 'sylius.form.variant.tracked',
            ])
            ->add('onHand', 'integer', [
                'label' => 'sylius.form.variant.on_hand',
            ])
            ->add('width', 'number', [
                'required' => false,
                'label' => 'sylius.form.variant.width',
                'invalid_message' => 'sylius.product_variant.width.invalid',
            ])
            ->add('height', 'number', [
                'required' => false,
                'label' => 'sylius.form.variant.height',
                'invalid_message' => 'sylius.product_variant.height.invalid',
            ])
            ->add('depth', 'number', [
                'required' => false,
                'label' => 'sylius.form.variant.depth',
                'invalid_message' => 'sylius.product_variant.depth.invalid',
            ])
            ->add('weight', 'number', [
                'required' => false,
                'label' => 'sylius.form.variant.weight',
                'invalid_message' => 'sylius.product_variant.weight.invalid',
            ])
            ->add('taxCategory', 'sylius_tax_category_choice', [
                'required' => false,
                'empty_value' => '---',
                'label' => 'sylius.form.product_variant.tax_category',
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
}
