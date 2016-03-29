<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ProvinceContext implements Context
{
    /**
     * @var FactoryInterface
     */
    private $provinceFactory;

    /**
     * @var RepositoryInterface
     */
    private $provinceRepository;

    /**
     * @param FactoryInterface $provinceFactory
     * @param RepositoryInterface $provinceRepository
     */
    public function __construct(FactoryInterface $provinceFactory, RepositoryInterface $provinceRepository)
    {
        $this->provinceFactory = $provinceFactory;
        $this->provinceRepository = $provinceRepository;
    }

    /**
     * @Given the store has a province :provinceName with code :code
     */
    public function theStoreHasAProvinceWithCode($provinceName, $code)
    {
        $province = $this->provinceFactory->createNew();
        $province->setName($provinceName);
        $province->setCode($code);

        $this->provinceRepository->add($province);
    }
}
