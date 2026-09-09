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

namespace Sylius\Bundle\CoreBundle\Installer\Setup;

use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;

final readonly class ZoneSetup implements ZoneSetupInterface
{
    /**
     * @param RepositoryInterface<ZoneInterface> $zoneRepository
     * @param FactoryInterface<ZoneInterface> $zoneFactory
     * @param FactoryInterface<ZoneMemberInterface> $zoneMemberFactory
     */
    public function __construct(
        private RepositoryInterface $zoneRepository,
        private FactoryInterface $zoneFactory,
        private FactoryInterface $zoneMemberFactory,
        private ObjectManager $zoneManager,
    ) {
    }

    public function setup(CountryInterface $country): ZoneInterface
    {
        /** @var ZoneInterface|null $zone */
        $zone = $this->zoneRepository->findOneBy([]);

        if (null === $zone) {
            /** @var ZoneInterface $zone */
            $zone = $this->zoneFactory->createNew();
            $zone->setCode('default');
            $zone->setName('Default');
            $zone->setType(ZoneInterface::TYPE_COUNTRY);

            $this->zoneManager->persist($zone);
        }

        if (!$this->zoneHasMemberWithCode($zone, (string) $country->getCode())) {
            /** @var ZoneMemberInterface $zoneMember */
            $zoneMember = $this->zoneMemberFactory->createNew();
            $zoneMember->setCode($country->getCode());

            $zone->addMember($zoneMember);
        }

        $this->zoneManager->flush();

        return $zone;
    }

    private function zoneHasMemberWithCode(ZoneInterface $zone, string $code): bool
    {
        foreach ($zone->getMembers() as $member) {
            if ($member->getCode() === $code) {
                return true;
            }
        }

        return false;
    }
}
