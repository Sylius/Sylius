<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Form\EventSubscriber;

use Sylius\Bundle\ProductBundle\Form\Type\ProductOptionChoiceType;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ProductOptionFieldSubscriber implements EventSubscriberInterface
{
    /**
     * @var ProductVariantResolverInterface
     */
    private $variantResolver;

    /**
     * @param ProductVariantResolverInterface $variantResolver
     */
    public function __construct(ProductVariantResolverInterface $variantResolver)
    {
        $this->variantResolver = $variantResolver;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        /** @var ProductInterface $product */
        $product = $event->getData();

        Assert::isInstanceOf($product, ProductInterface::class);

        $form = $event->getForm();

        /** Options should be disabled for configurable product if it has at least one defined variant */
        $disableOptions = (null !== $this->variantResolver->getVariant($product)) && $product->hasVariants();

        $form->add('options', ProductOptionChoiceType::class, [
            'required' => false,
            'disabled' => $disableOptions,
            'multiple' => true,
            'label' => 'sylius.form.product.options',
        ]);
    }
}
