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

namespace Sylius\Bundle\AttributeBundle\EventListener;

use Sylius\Component\Attribute\Checker\AttributeDeletionCheckerInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Webmozart\Assert\Assert;

final class RemoveAttributeListener
{
    public function __construct(
        private RequestStack $requestStack,
        private AttributeDeletionCheckerInterface $attributeDeletionChecker,
    ) {
    }

    public function protectFromRemovingAttribute(GenericEvent $event): void
    {
        $productAttribute = $event->getSubject();
        Assert::isInstanceOf($productAttribute, ProductAttributeInterface::class);

        if (!$this->attributeDeletionChecker->isDeletable($productAttribute)) {
            /** @var FlashBagInterface $flashes */
            $flashes = $this->getSession()->getBag('flashes');
            $flashes->add('error', [
                'message' => 'sylius.resource.delete_error',
                'parameters' => ['%resource%' => 'attribute'],
            ]);

            $event->stopPropagation();
        }
    }

    private function getSession(): SessionInterface
    {
        if (!method_exists(RequestStack::class, 'getSession')) {
            return $this->requestStack->getMasterRequest()->getSession();
        }

        return $this->requestStack->getSession();
    }
}
