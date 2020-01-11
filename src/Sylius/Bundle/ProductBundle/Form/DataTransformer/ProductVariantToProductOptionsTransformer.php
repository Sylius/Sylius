<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ProductBundle\Form\DataTransformer;

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

final class ProductVariantToProductOptionsTransformer implements DataTransformerInterface
{
    /** @var ProductInterface */
    private $product;

    public function __construct(ProductInterface $product)
    {
        $this->product = $product;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UnexpectedTypeException
     */
    public function transform($value): array
    {
        if (null === $value) {
            return [];
        }

        if (!$value instanceof ProductVariantInterface) {
            throw new UnexpectedTypeException($value, ProductVariantInterface::class);
        }

        return array_combine(
            array_map(
                function (ProductOptionValueInterface $productOptionValue): string {
                    return (string) $productOptionValue->getOptionCode();
                },
                $value->getOptionValues()->toArray()
            ),
            $value->getOptionValues()->toArray()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value): ?ProductVariantInterface
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (!is_array($value) && !$value instanceof \Traversable && !$value instanceof \ArrayAccess) {
            throw new UnexpectedTypeException($value, '\Traversable or \ArrayAccess');
        }

        return $this->matches(is_array($value) ? $value : iterator_to_array($value));
    }

    /**
     * @param (ProductOptionValueInterface|null)[] $optionValues
     *
     * @throws TransformationFailedException
     */
    private function matches(array $optionValues): ProductVariantInterface
    {
        foreach ($this->product->getVariants() as $variant) {
            foreach ($optionValues as $optionValue) {
                if (null === $optionValue || !$variant->hasOptionValue($optionValue)) {
                    continue 2;
                }
            }

            return $variant;
        }

        throw new TransformationFailedException(sprintf(
            'Variant "%s" not found for product %s',
            !empty($optionValues[0]) ? $optionValues[0]->getCode() : '',
            $this->product->getCode()
        ));
    }
}
