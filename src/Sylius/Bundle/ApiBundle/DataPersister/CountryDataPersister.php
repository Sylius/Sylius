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

namespace Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Bundle\ApiBundle\Exception\ProvinceCannotBeRemoved;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/** @experimental */
final class CountryDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private ContextAwareDataPersisterInterface $decoratedDataPersister,
        private RepositoryInterface $provinceRepository,
        private RepositoryInterface $zoneMemberRepository
    ) {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof CountryInterface;
    }

    public function persist($data, array $context = [])
    {
        $provinces = $this->provinceRepository->findBy(['country' => $data]);

        $countryProvinceCodes = $data->getProvinces()->map(fn ($province): string => $province->getCode())->getValues();
        $provinceCodes = (new ArrayCollection($provinces))->map(fn ($province): string => $province->getCode())->getValues();

        $provincesToDelete = array_diff($provinceCodes, $countryProvinceCodes);

        $zoneMember = $this->zoneMemberRepository->findOneBy(['code' => $provincesToDelete]);

        if (null !== $zoneMember) {
            throw new ProvinceCannotBeRemoved();
        }

        return $this->decoratedDataPersister->persist($data, $context);
    }

    public function remove($data, array $context = [])
    {
        return $this->decoratedDataPersister->remove($data, $context);
    }
}
