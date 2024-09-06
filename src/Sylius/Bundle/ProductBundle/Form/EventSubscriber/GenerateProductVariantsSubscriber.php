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

use Sylius\Component\Product\Generator\ProductVariantGeneratorInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Exception\VariantWithNoOptionsValuesException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Webmozart\Assert\Assert;

final readonly class GenerateProductVariantsSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ProductVariantGeneratorInterface $generator,
        private RequestStack $requestStack,
    ) {
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

        try {
            $this->generator->generate($product);
        } catch (VariantWithNoOptionsValuesException $exception) {
            $this->getFlashBag()->add('error', $exception->getMessage());
        }
    }

    private function getFlashBag(): FlashBagInterface
    {
        $flashBag = $this->requestStack->getSession()->getBag('flashes');
        Assert::isInstanceOf($flashBag, FlashBagInterface::class);

        return $flashBag;
    }
}
