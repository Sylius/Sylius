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

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

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
     * @var RepositoryInterface
     */
    private $productTaxonRepository;

    /**
     * @var ProductInterface
     */
    private $product;

    /**
     * @param FactoryInterface $productTaxonFactory
     * @param RepositoryInterface $productTaxonRepository
     * @param ProductInterface $product
     */
    public function __construct(
        FactoryInterface $productTaxonFactory,
        RepositoryInterface $productTaxonRepository,
        ProductInterface $product
    ) {
        $this->productTaxonFactory = $productTaxonFactory;
        $this->productTaxonRepository = $productTaxonRepository;
        $this->product = $product;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($productTaxon)
    {
        if (null === $productTaxon) {
            return null;
        }

        $this->assertTransformationValueType($productTaxon, ProductTaxonInterface::class);

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

        $this->assertTransformationValueType($taxon, TaxonInterface::class);

        /** @var ProductTaxonInterface $productTaxon */
        $productTaxon = $this->productTaxonRepository->findOneBy(['taxon' => $taxon, 'product' => $this->product]);

        if (null === $productTaxon) {
            $productTaxon = $this->productTaxonFactory->createNew();
            $productTaxon->setProduct($this->product);
            $productTaxon->setTaxon($taxon);
        }

        return $productTaxon;
    }

    /**
     * @param string $value
     * @param string $expectedType
     *
     * @throws TransformationFailedException
     */
    private function assertTransformationValueType($value, $expectedType)
    {
        if (!($value instanceof $expectedType)) {
            throw new TransformationFailedException(
                sprintf(
                    'Expected "%s", but got "%s"',
                    $expectedType,
                    is_object($value) ? get_class($value) : gettype($value)
                )
            );
        }
    }
}
