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

use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class ProvinceProvider implements ProvinceProviderInterface
{
    /** @var RepositoryInterface */
    private $provinceRepository;

    public function __construct(RepositoryInterface $provinceRepository)
    {
        $this->provinceRepository = $provinceRepository;
    }

    public function findByName(string $provinceName): ?ProvinceInterface
    {
        /** @var ProvinceInterface $province */
        $province = $this->provinceRepository->findOneBy(['name' => $provinceName]);

        return $province;
    }
}
