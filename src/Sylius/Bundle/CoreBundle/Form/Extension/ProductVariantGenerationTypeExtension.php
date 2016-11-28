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
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantGenerationType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ProductVariantGenerationTypeExtension extends AbstractTypeExtension
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
        return ProductVariantGenerationType::class;
    }
}
