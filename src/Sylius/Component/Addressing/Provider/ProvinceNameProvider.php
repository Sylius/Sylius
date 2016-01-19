<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Addressing\Provider;

use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ProvinceNameProvider implements ProvinceNameProviderInterface
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
    public function get($provinceCode)
    {
        /** @var ProvinceInterface $province */
        $province = $this->provinceRepository->findOneBy(array('code' => $provinceCode));

        if (null === $province) {
            throw new \InvalidArgumentException(sprintf('Province with code "%s" not found.', $provinceCode));
        }

        return $province->getName();
    }
}
