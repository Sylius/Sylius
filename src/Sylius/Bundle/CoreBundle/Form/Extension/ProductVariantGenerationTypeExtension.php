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

use Sylius\Bundle\CoreBundle\Form\Type\ChannelCollectionType;
use Sylius\Bundle\CoreBundle\Form\Type\Product\ChannelPricingType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantGenerationType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ProductVariantGenerationTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $event->getForm()->add('channelPricings', ChannelCollectionType::class, [
                'entry_type' => ChannelPricingType::class,
                'entry_options' => [
                    'product_variant' => $event->getData(),
                ],
                'label' => 'sylius.form.variant.price',
            ]);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return ProductVariantGenerationType::class;
    }
}
