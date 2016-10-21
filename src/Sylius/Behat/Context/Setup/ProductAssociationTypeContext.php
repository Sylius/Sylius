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
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Association\Model\AssociationTypeInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ProductAssociationTypeContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var FactoryInterface
     */
    private $productAssociationTypeFactory;

    /**
     * @var RepositoryInterface
     */
    private $productAssociationTypeRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param FactoryInterface $productAssociationTypeFactory
     * @param RepositoryInterface $productAssociationTypeRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $productAssociationTypeFactory,
        RepositoryInterface $productAssociationTypeRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productAssociationTypeFactory = $productAssociationTypeFactory;
        $this->productAssociationTypeRepository = $productAssociationTypeRepository;
    }

    /**
     * @Given the store has (also) a product association type :name
     */
    public function theStoreHasAProductAssociationType($name)
    {
        $this->createProductAssociationType($name);
    }

    /**
     * @param string $name
     * @param string|null $code
     */
    private function createProductAssociationType($name, $code = null)
    {
        if (null === $code) {
            $code = $this->generateCodeFromName($name);
        }

        /** @var AssociationTypeInterface $productAssociationType */
        $productAssociationType = $this->productAssociationTypeFactory->createNew();
        $productAssociationType->setCode($code);
        $productAssociationType->setName(ucfirst($name));

        $this->productAssociationTypeRepository->add($productAssociationType);
        $this->sharedStorage->set('product_association_Type', $productAssociationType);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function generateCodeFromName($name)
    {
        return str_replace([' ', '-'], '_', strtolower($name));
    }
}
