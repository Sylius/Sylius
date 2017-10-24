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

namespace Sylius\Component\Addressing\Provider;

use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

class ProvinceNamingProvider implements ProvinceNamingProviderInterface
{
    /**
     * @var RepositoryInterface
     */
    private $provinceRepository;

    /**
     * @param RepositoryInterface $provinceRepository
     */
    public function __construct(RepositoryInterface $provinceRepository)
    {
        $this->provinceRepository = $provinceRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(AddressInterface $address): string
    {
        if (null !== $address->getProvinceName()) {
            return $address->getProvinceName();
        }

        if (null === $address->getProvinceCode()) {
            return '';
        }

        $province = $this->provinceRepository->findOneBy(['code' => $address->getProvinceCode()]);
        Assert::notNull($province, sprintf('Province with code "%s" not found.', $address->getProvinceCode()));

        return $province->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getAbbreviation(AddressInterface $address): string
    {
        if (null !== $address->getProvinceName()) {
            return $address->getProvinceName();
        }

        if (null === $address->getProvinceCode()) {
            return '';
        }

        $province = $this->provinceRepository->findOneBy(['code' => $address->getProvinceCode()]);
        Assert::notNull($province, sprintf('Province with code "%s" not found.', $address->getProvinceCode()));

        return $province->getAbbreviation() ?: $province->getName();
    }
}
