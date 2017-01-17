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
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ProductTaxonContext implements Context
{
    /**
     * @var FactoryInterface
     */
    private $productTaxonFactory;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param FactoryInterface $productTaxonFactory
     * @param ObjectManager $objectManager
     */
    public function __construct(
        FactoryInterface $productTaxonFactory,
        ObjectManager $objectManager
    ) {
        $this->productTaxonFactory = $productTaxonFactory;
        $this->objectManager = $objectManager;
    }

    /**
     * @Given /^I assigned (this product) to ("[^"]+" taxon)$/
     * @Given /^(it|this product) (belongs to "[^"]+")$/
     * @Given /^(this product) is in ("[^"]+" taxon) at (\d)(?:st|nd|rd|th) position$/
     */
    public function itBelongsTo(ProductInterface $product, TaxonInterface $taxon, $position = null)
    {
        $productTaxon = $this->createProductTaxon($taxon, $product, (int) $position - 1);
        $product->addProductTaxon($productTaxon);

        $this->objectManager->flush($product);
    }
    
    /**
     * @param TaxonInterface $taxon
     * @param ProductInterface $product
     * @param int|null $position
     *
     * @return ProductTaxonInterface
     */
    private function createProductTaxon(TaxonInterface $taxon, ProductInterface $product, $position = null)
    {
        /** @var ProductTaxonInterface $productTaxon */
        $productTaxon = $this->productTaxonFactory->createNew();
        $productTaxon->setProduct($product);
        $productTaxon->setTaxon($taxon);

        if (null !== $position) {
            $productTaxon->setPosition($position);
        }

        return $productTaxon;
    }
}
