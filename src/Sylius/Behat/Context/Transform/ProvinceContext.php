<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ProvinceContext implements Context
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
     * @Transform /^province "([^"]+)"$/
     * @Transform /^"([^"]+)" province$/
     */
    public function getProvinceByName($provinceName)
    {
        $province = $this->provinceRepository->findOneBy(['name' => $provinceName]);
        Assert::notNull(
            $province,
            sprintf('Province with name "%s" does not exist', $provinceName)
        );

        return $province;
    }
}
