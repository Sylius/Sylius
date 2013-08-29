<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Builder;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\ProductBundle\Model\ProductInterface;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;

/**
 * Product builder with fluent interface.
 *
 * Usage example:
 *
 * <code>
 * <?php
 * $this->get('sylius.product_builder')
 *     ->create('Github mug')
 *     ->setDescription("Coffee. Tea. Coke. Water. Let's face it — humans need to drink liquids")
 *     ->setPrice(1200)
 *     ->addProperty('collection', 2013)
 *     ->save()
 * ;
 * </code>
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ProductBuilder implements ProductBuilderInterface
{
    /**
     * Currently built product.
     *
     * @var ProductInterface
     */
    protected $product;

    /**
     * Product object manager.
     *
     * @var ObjectManager
     */
    protected $productManager;

    /**
     * Product repository.
     *
     * @var RepositoryInterface
     */
    protected $productRepository;

    /**
     * Property repository.
     *
     * @var RepositoryInterface
     */
    protected $propertyRepository;

    /**
     * Product property repository.
     *
     * @var RepositoryInterface
     */
    protected $productPropertyRepository;

    public function __construct(
        ObjectManager      $productManager,
        RepositoryInterface $productRepository,
        RepositoryInterface $propertyRepository,
        RepositoryInterface $productPropertyRepository
    )
    {
        $this->productManager            = $productManager;
        $this->productRepository         = $productRepository;
        $this->propertyRepository        = $propertyRepository;
        $this->productPropertyRepository = $productPropertyRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function __call($method, $arguments)
    {
        if (!method_exists($this->product, $method)) {
            throw new \BadMethodCallException(sprintf('Product has no %s() method.', $method));
        }

        call_user_func_array(array($this->product, $method), $arguments);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function create($name)
    {
        $this->product = $this->productRepository->createNew();
        $this->product->setName($name);

        return $this;
    }

    public function addProperty($name, $value, $presentation = null)
    {
        $property = $this->propertyRepository->findOneBy(array('name' => $name));

        if (null === $property) {
            $property = $this->propertyRepository->createNew();

            $property->setName($name);
            $property->setPresentation($presentation ?: $name);

            $this->productManager->persist($property);
            $this->productManager->flush($property);
        }

        $productProperty = $this->productPropertyRepository->createNew();

        $productProperty->setProperty($property);
        $productProperty->setValue($value);

        $this->product->addProperty($productProperty);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function save($flush = true)
    {
        $this->productManager->persist($this->product);

        if ($flush) {
            $this->productManager->flush($this->product);
        }

        return $this->product;
    }
}
