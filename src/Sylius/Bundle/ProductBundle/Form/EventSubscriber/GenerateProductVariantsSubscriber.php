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

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Variation\Generator\VariantGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class GenerateProductVariantsSubscriber implements EventSubscriberInterface
{
    /**
     * @var VariantGeneratorInterface
     */
    private $generator;

    /**
     * @param VariantGeneratorInterface $generator
     */
    public function __construct(VariantGeneratorInterface $generator)
    {
        $this->generator = $generator;
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

        $this->generator->generate($product);
    }
}
