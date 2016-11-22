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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class ProductTaxonCollectionToTaxonCollectionTransformer implements DataTransformerInterface
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
    public function transform($productTaxonCollection)
    {
        if(null === $productTaxonCollection) {
            return new ArrayCollection();
        }

        if (!$productTaxonCollection instanceof Collection) {
            throw new UnexpectedTypeException($productTaxonCollection, Collection::class);
        }

        $taxonCollection = new ArrayCollection();

        foreach ($productTaxonCollection as $productTaxon) {
            $taxonCollection->add($productTaxon->getTaxon());
        }

        return $taxonCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($taxonCollection)
    {
        if(null === $taxonCollection) {
            return new ArrayCollection();
        }

        if (!$taxonCollection instanceof Collection) {
            throw new UnexpectedTypeException($taxonCollection, Collection::class);
        }

        $productTaxonCollection = new ArrayCollection();

        foreach ($taxonCollection as $taxon) {
            $productTaxon = $this->productTaxonFactory->createNew();
            $productTaxon->setTaxon($taxon);
            $productTaxonCollection->add($productTaxon);
        }

        return $productTaxonCollection;
    }
}
