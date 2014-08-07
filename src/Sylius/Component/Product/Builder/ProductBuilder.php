<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Builder;

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Manager\DomainManagerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

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
 *     ->addAttribute('collection', 2013)
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
     * @var DomainManagerInterface
     */
    protected $productManager;

    /**
     * Product repository.
     *
     * @var RepositoryInterface
     */
    protected $productRepository;

    /**
     * Product attribute repository.
     *
     * @var RepositoryInterface
     */
    protected $attributeRepository;

    /**
     * Product attribute manager.
     *
     * @var DomainManagerInterface
     */
    protected $attributeManager;

    /**
     * Product attribute value manager.
     *
     * @var DomainManagerInterface
     */
    protected $attributeValueManager;

    public function __construct(
        DomainManagerInterface $productManager,
        RepositoryInterface    $attributeRepository,
        DomainManagerInterface $attributeManager,
        DomainManagerInterface $attributeValueManager
    )
    {
        $this->productManager        = $productManager;
        $this->attributeRepository   = $attributeRepository;
        $this->attributeManager      = $attributeManager;
        $this->attributeValueManager = $attributeValueManager;
    }

    /**
     * {@inheritdoc}
     */
    public function __call($method, $arguments)
    {
        if (!method_exists($this->product, $method)) {
            throw new \BadMethodCallException(sprintf('Product has no "%s()" method.', $method));
        }

        call_user_func_array(array($this->product, $method), $arguments);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function create($name)
    {
        $this->product = $this->productManager->createNew();
        $this->product->setName($name);

        return $this;
    }

    public function addAttribute($name, $value, $presentation = null)
    {
        $attribute = $this->attributeRepository->findOneBy(array('name' => $name));

        if (null === $attribute) {
            $attribute = $this->attributeManager->createNew();
            $attribute->setName($name);
            $attribute->setPresentation($presentation ?: $name);

            $this->productManager->create($attribute);
        }

        $attributeValue = $this->attributeValueManager->createNew();
        $attributeValue->setAttribute($attribute);
        $attributeValue->setValue($value);

        $this->product->addAttribute($attributeValue);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $this->productManager->create($this->product);

        return $this->product;
    }
}
