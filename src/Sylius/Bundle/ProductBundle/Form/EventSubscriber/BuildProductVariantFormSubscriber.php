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

namespace Sylius\Bundle\ProductBundle\Form\EventSubscriber;

use Sylius\Bundle\ProductBundle\Form\Type\ProductOptionValueCollectionType;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

final class BuildProductVariantFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * @var bool
     */
    private $disabled;

    /**
     * @param FormFactoryInterface $factory
     * @param bool $disabled
     */
    public function __construct(FormFactoryInterface $factory, bool $disabled = false)
    {
        $this->factory = $factory;
        $this->disabled = $disabled;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event): void
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $event->getData();
        $form = $event->getForm();

        if (null === $productVariant) {
            return;
        }

        $product = $productVariant->getProduct();

        if (!$product->hasOptions()) {
            return;
        }

        $form->add($this->factory->createNamed(
            'optionValues',
            ProductOptionValueCollectionType::class,
            $productVariant->getOptionValues(),
            [
                'disabled' => $this->disabled,
                'options' => $product->getOptions(),
                'auto_initialize' => false,
            ]
        ));
    }
}
