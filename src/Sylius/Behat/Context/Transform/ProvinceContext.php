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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class ProvinceContext implements Context
{
    public function __construct(private RepositoryInterface $provinceRepository)
    {
    }

    /**
     * @Transform /^province "([^"]+)"$/
     * @Transform /^"([^"]+)" province$/
     * @Transform /^province as "([^"]+)"$/
     * @Transform :province
     */
    public function getProvinceByName(string $provinceName): ProvinceInterface
    {
        /** @var ProvinceInterface|null $province */
        $province = $this->provinceRepository->findOneBy(['name' => $provinceName]);
        Assert::notNull(
            $province,
            sprintf('Province with name "%s" does not exist', $provinceName),
        );

        return $province;
    }

    /**
     * @Transform /^"([^"]*)" and "([^"]*)" provinces$/
     */
    public function getProvincesByName(string ...$provinceNames): array
    {
        return $this->provinceRepository->findBy(['name' => $provinceNames]);
    }
}
