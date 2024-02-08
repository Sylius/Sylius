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

namespace Sylius\Bundle\AddressingBundle\EventListener;

use Sylius\Component\Addressing\Checker\CountryProvincesDeletionCheckerInterface;
use Sylius\Component\Addressing\Checker\ZoneDeletionCheckerInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Webmozart\Assert\Assert;

final class ZoneMemberIntegrityListener
{
    public function __construct(
        private RequestStack $requestStack,
        private ZoneDeletionCheckerInterface $zoneDeletionChecker,
        private CountryProvincesDeletionCheckerInterface $countryProvincesDeletionChecker,
    ) {
    }

    public function protectFromRemovingZone(GenericEvent $event): void
    {
        $zone = $event->getSubject();
        Assert::isInstanceOf($zone, ZoneInterface::class);

        if (!$this->zoneDeletionChecker->isDeletable($zone)) {
            /** @var FlashBagInterface $flashes */
            $flashes = $this->getSession()->getBag('flashes');
            $flashes->add('error', [
                'message' => 'sylius.resource.delete_error',
                'parameters' => ['%resource%' => 'zone'],
            ]);

            $event->stopPropagation();
        }
    }

    public function protectFromRemovingProvinceWithinCountry(GenericEvent $event): void
    {
        /** @var CountryInterface $country */
        $country = $event->getSubject();
        Assert::isInstanceOf($country, CountryInterface::class);

        if (!$this->countryProvincesDeletionChecker->isDeletable($country)) {
            /** @var FlashBagInterface $flashes */
            $flashes = $this->getSession()->getBag('flashes');
            $flashes->add('error', [
                'message' => 'sylius.resource.delete_error',
                'parameters' => ['%resource%' => 'province'],
            ]);

            $event->stopPropagation();
        }
    }

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }
}
