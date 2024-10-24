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

namespace Sylius\Bundle\ShopBundle\EventListener;

use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\ShopBundle\SectionResolver\ShopCustomerAccountSubSection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ShopCustomerAccountSubSectionCacheControlSubscriber implements EventSubscriberInterface
{
    /** @var SectionProviderInterface */
    private $sectionProvider;

    public function __construct(SectionProviderInterface $sectionProvider)
    {
        $this->sectionProvider = $sectionProvider;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'setCacheControlDirectives',
        ];
    }

    public function setCacheControlDirectives(ResponseEvent $event): void
    {
        if (!$this->sectionProvider->getSection() instanceof ShopCustomerAccountSubSection) {
            return;
        }

        $response = $event->getResponse();

        $response->headers->addCacheControlDirective('no-cache', true);
        $response->headers->addCacheControlDirective('max-age', '0');
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->addCacheControlDirective('no-store', true);
    }
}
