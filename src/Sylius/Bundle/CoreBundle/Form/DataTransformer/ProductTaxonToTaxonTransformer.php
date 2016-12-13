<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\DataTransformer;

use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Webmozart\Assert\Assert;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ProductTaxonToTaxonTransformer implements DataTransformerInterface
{
    /**
     * @var FactoryInterface
     */
    private $productTaxonFactory;

    /**
     * @param FactoryInterface $productTaxonFactory
     */
    public function __construct(FactoryInterface $productTaxonFactory)
    {
        $this->productTaxonFactory = $productTaxonFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($productTaxon)
    {
        if (null === $productTaxon) {
            return null;
        }

        Assert::isInstanceOf($productTaxon, ProductTaxonInterface::class);

        return $productTaxon->getTaxon();
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($taxon)
    {
        if (null === $taxon) {
            return null;
        }

        Assert::isInstanceOf($taxon, TaxonInterface::class);

        $productTaxon = $this->productTaxonFactory->createNew();
        $productTaxon->setTaxon($taxon);

        return $productTaxon;
    }
}
