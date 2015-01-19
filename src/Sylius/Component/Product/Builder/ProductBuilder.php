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
use Sylius\Component\Resource\Factory\ResourceFactoryInterface;
use Sylius\Component\Resource\Manager\ResourceManagerInterface;
use Sylius\Component\Resource\Repository\ResourceRepositoryInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ProductBuilder implements ProductBuilderInterface
{
    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var ResourceManagerInterface
     */
    protected $productManager;

    /**
     * @var ResourceFactoryInterface
     */
    protected $productFactory;

    /**
     * @var ResourceRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var ResourceFactoryInterface
     */
    protected $attributeFactory;

    /**
     * Product attribute factory.
     *
     * @var ResourceRepositoryInterface
     */
    protected $attributeValueFactory;

    public function __construct(
        ResourceManagerInterface    $productManager,
        ResourceFactoryInterface    $productFactory,
        ResourceRepositoryInterface $attributeRepository,
        ResourceFactoryInterface    $attributeFactory,
        ResourceFactoryInterface    $attributeValueFactory
    )
    {
        $this->productManager        = $productManager;
        $this->productFactory        = $productFactory;
        $this->attributeRepository   = $attributeRepository;
        $this->attributeFactory      = $attributeFactory;
        $this->attributeValueFactory = $attributeValueFactory;
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
        $this->product = $this->productFactory->createNew();
        $this->product->setName($name);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addAttribute($name, $value, $presentation = null)
    {
        $attribute = $this->attributeRepository->findOneBy(array('name' => $name));

        if (null === $attribute) {
            $attribute = $this->attributeFactory->createNew();

            $attribute->setName($name);
            $attribute->setPresentation($presentation ?: $name);
        }

        $attributeValue = $this->attributeValueFactory->createNew();

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
        $this->productManager->persist($this->product);
        $this->productManager->flush();

        return $this->product;
    }
}
