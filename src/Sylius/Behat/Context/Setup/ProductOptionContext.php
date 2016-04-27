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
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Product\Model\OptionInterface;
use Sylius\Component\Product\Model\OptionValueInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Variation\Repository\OptionRepositoryInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ProductOptionContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var OptionRepositoryInterface
     */
    private $productOptionRepository;

    /**
     * @var FactoryInterface
     */
    private $productOptionFactory;

    /**
     * @var FactoryInterface
     */
    private $productOptionValueFactory;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param OptionRepositoryInterface $productOptionRepository
     * @param FactoryInterface $productOptionFactory
     * @param FactoryInterface $productOptionValueFactory
     * @param ObjectManager $objectManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        OptionRepositoryInterface $productOptionRepository,
        FactoryInterface $productOptionFactory,
        FactoryInterface $productOptionValueFactory,
        ObjectManager $objectManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productOptionRepository = $productOptionRepository;
        $this->productOptionFactory = $productOptionFactory;
        $this->productOptionValueFactory = $productOptionValueFactory;
        $this->objectManager = $objectManager;
    }

    /**
     * @Given the store has a product option :productOptionName with a code :productOptionCode
     */
    public function theStoreHasAProductOptionWithACode($productOptionName, $productOptionCode)
    {
        $productOption = $this->productOptionFactory->createNew();
        $productOption->setCode($productOptionCode);
        $productOption->setName($productOptionName);

        $this->sharedStorage->set('product_option', $productOption);
        $this->productOptionRepository->add($productOption);
    }

    /**
     * @Given /^(this product option) has(?:| also) the "([^"]+)" option value with code "([^"]+)"$/
     */
    public function thisProductOptionHasTheOptionValueWithCode(
        OptionInterface $productOption,
        $productOptionValueName,
        $productOptionValueCode
    ) {
        $productOptionValue = $this->createProductOptionValue($productOptionValueName, $productOptionValueCode);
        $productOption->addValue($productOptionValue);

        $this->objectManager->flush();
    }

    /**
     * @param string $value
     * @param string $code
     *
     * @return OptionValueInterface
     */
    private function createProductOptionValue($value, $code)
    {
        $productOptionValue = $this->productOptionValueFactory->createNew();
        $productOptionValue->setValue($value);
        $productOptionValue->setCode($code);

        return $productOptionValue;
    }
}
