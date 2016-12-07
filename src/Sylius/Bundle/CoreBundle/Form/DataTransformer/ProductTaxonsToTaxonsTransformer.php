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

use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ProductTaxonsToTaxonsTransformer implements DataTransformerInterface
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
    public function transform($productTaxons)
    {
        if(null === $productTaxons) {
            return [];
        }

        $taxons = [];

        foreach ($productTaxons as $productTaxon) {
            $taxons[] = $productTaxon->getTaxon();
        }

        return $taxons;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($taxons)
    {
        if(null === $taxons) {
            return [];
        }

        $productTaxons = [];

        foreach ($taxons as $taxon) {
            $productTaxon = $this->productTaxonFactory->createNew();
            $productTaxon->setTaxon($taxon);
            $productTaxons[] = $productTaxon;
        }

        return $productTaxons;
    }
}
