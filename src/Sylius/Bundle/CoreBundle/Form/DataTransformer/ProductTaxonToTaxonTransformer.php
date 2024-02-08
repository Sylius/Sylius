<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Form\DataTransformer;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class ProductTaxonToTaxonTransformer implements DataTransformerInterface
{
    public function __construct(
        private FactoryInterface $productTaxonFactory,
        private RepositoryInterface $productTaxonRepository,
        private ProductInterface $product,
    ) {
    }

    public function transform($value): ?TaxonInterface
    {
        if (null === $value) {
            return null;
        }

        $this->assertTransformationValueType($value, ProductTaxonInterface::class);

        return $value->getTaxon();
    }

    public function reverseTransform($value): ?ProductTaxonInterface
    {
        if (null === $value) {
            return null;
        }

        $this->assertTransformationValueType($value, TaxonInterface::class);

        /** @var ProductTaxonInterface|null $productTaxon */
        $productTaxon = $this->productTaxonRepository->findOneBy(['taxon' => $value, 'product' => $this->product]);

        if (null === $productTaxon) {
            /** @var ProductTaxonInterface $productTaxon */
            $productTaxon = $this->productTaxonFactory->createNew();
            $productTaxon->setProduct($this->product);
            $productTaxon->setTaxon($value);
        }

        return $productTaxon;
    }

    /**
     * @throws TransformationFailedException
     */
    private function assertTransformationValueType($value, string $expectedType): void
    {
        if (!($value instanceof $expectedType)) {
            throw new TransformationFailedException(
                sprintf(
                    'Expected "%s", but got "%s"',
                    $expectedType,
                    get_debug_type($value),
                ),
            );
        }
    }
}
