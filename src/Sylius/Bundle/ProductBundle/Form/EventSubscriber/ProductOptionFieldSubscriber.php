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

namespace Sylius\Bundle\ProductBundle\Form\EventSubscriber;

use Sylius\Bundle\ProductBundle\Form\Type\ProductOptionAutocompleteType;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Webmozart\Assert\Assert;

final class ProductOptionFieldSubscriber implements EventSubscriberInterface
{
    public function __construct(private ProductVariantResolverInterface $variantResolver)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    public function preSetData(FormEvent $event): void
    {
        $product = $event->getData();

        /** @var ProductInterface $product */
        Assert::isInstanceOf($product, ProductInterface::class);

        $form = $event->getForm();

        /** Options should be disabled for configurable product if it has at least one defined variant */
        $disableOptions = null !== $this->variantResolver->getVariant($product);

        $form->add('options', ProductOptionAutocompleteType::class, [
            'required' => false,
            'disabled' => $disableOptions,
            'multiple' => true,
            'label' => 'sylius.form.product.options',
        ]);
    }
}
