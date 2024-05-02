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

namespace Sylius\Bundle\ApiBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ProductSlugEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private SlugGeneratorInterface $slugGenerator)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['generateSlug', EventPriorities::PRE_VALIDATE],
        ];
    }

    public function generateSlug(ViewEvent $event): void
    {
        $product = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (
            !$product instanceof ProductInterface ||
            !in_array($method, [Request::METHOD_POST, Request::METHOD_PUT], true)
        ) {
            return;
        }

        /** @var ProductTranslationInterface $productTranslation */
        foreach ($product->getTranslations() as $productTranslation) {
            if ($productTranslation->getSlug() !== null && $productTranslation->getSlug() !== '') {
                continue;
            }

            if ($productTranslation->getName() === null || $productTranslation->getName() === '') {
                continue;
            }

            $productTranslation->setSlug($this->slugGenerator->generate($productTranslation->getName()));
        }

        $event->setControllerResult($product);
    }
}
