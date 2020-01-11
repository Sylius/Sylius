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

use Sylius\Component\Product\Generator\ProductVariantGeneratorInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Exception\VariantWithNoOptionsValuesException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Session\Session;
use Webmozart\Assert\Assert;

final class GenerateProductVariantsSubscriber implements EventSubscriberInterface
{
    /** @var ProductVariantGeneratorInterface */
    private $generator;

    /** @var Session */
    private $session;

    public function __construct(ProductVariantGeneratorInterface $generator, Session $session)
    {
        $this->generator = $generator;
        $this->session = $session;
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

    public function preSetData(FormEvent $event): void
    {
        $product = $event->getData();

        /** @var ProductInterface $product */
        Assert::isInstanceOf($product, ProductInterface::class);

        try {
            $this->generator->generate($product);
        } catch (VariantWithNoOptionsValuesException $exception) {
            $this->session->getFlashBag()->add('error', $exception->getMessage());
        }
    }
}
