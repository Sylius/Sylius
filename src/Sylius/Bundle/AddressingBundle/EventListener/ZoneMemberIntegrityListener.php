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

namespace Sylius\Bundle\AddressingBundle\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Webmozart\Assert\Assert;

final class ZoneMemberIntegrityListener
{
    public function __construct(
        private SessionInterface $session,
        private RepositoryInterface $zoneMemberRepository,
        private RepositoryInterface $provinceRepository,
    ) {
    }

    public function protectFromRemovingZone(GenericEvent $event): void
    {
        $zone = $event->getSubject();
        Assert::isInstanceOf($zone, ZoneInterface::class);

        $zoneMember = $this->zoneMemberRepository->findOneBy(['code' => $zone->getCode()]);

        if (null !== $zoneMember) {
            /** @var FlashBagInterface $flashes */
            $flashes = $this->session->getBag('flashes');
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
        $provinces = $this->provinceRepository->findBy(['country' => $country]);

        $countryProvinceCodes = $country->getProvinces()->map(fn ($province): string => $province->getCode())->getValues();
        $provinceCodes = (new ArrayCollection($provinces))->map(fn ($province): string => $province->getCode())->getValues();

        $provincesToDelete = array_diff($provinceCodes, $countryProvinceCodes);

        $zoneMember = $this->zoneMemberRepository->findOneBy(['code' => $provincesToDelete]);

        if (null !== $zoneMember) {
            /** @var FlashBagInterface $flashes */
            $flashes = $this->session->getBag('flashes');
            $flashes->add('error', [
                'message' => 'sylius.resource.delete_error',
                'parameters' => ['%resource%' => 'province'],
            ]);

            $event->stopPropagation();
        }
    }
}
