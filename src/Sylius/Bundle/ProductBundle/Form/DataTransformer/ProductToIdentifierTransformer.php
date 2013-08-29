<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Bundle\ProductBundle\Model\ProductInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Product to id transformer.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ProductToIdentifierTransformer implements DataTransformerInterface
{
    /**
     * Product repository.
     *
     * @var ObjectRepository
     */
    private $productRepository;

    /**
     * Identifier.
     *
     * @var string
     */
    private $identifier;

    /**
     * Constructor.
     *
     * @param ObjectRepository $productRepository
     * @param string           $identifier
     */
    public function __construct(ObjectRepository $productRepository, $identifier)
    {
        $this->productRepository = $productRepository;
        $this->identifier = $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($product)
    {
        if (null === $product) {
            return '';
        }

        if (!$product instanceof ProductInterface) {
            throw new UnexpectedTypeException($product, 'Sylius\Bundle\ProductBundle\Model\ProductInterface');
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        return $accessor->getValue($product, $this->identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (!$value) {
            return null;
        }

        return $this->productRepository->findOneBy(array($this->identifier => $value));
    }
}
