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
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ProductsToCodesTransformer implements DataTransformerInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (!is_array($value) && !is_null($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        if (empty($value)) {
            return new ArrayCollection();
        }

        return new ArrayCollection($this->productRepository->findBy(['code' => $value]));
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($products)
    {
        if (!$products instanceof Collection) {
            throw new UnexpectedTypeException($products, Collection::class);
        }

        if (null === $products) {
            return [];
        }

        $productCodes = [];

        /** @var ProductInterface $product */
        foreach ($products as $product) {
            $productCodes[] = $product->getCode();
        }

        return $productCodes;
    }
}
