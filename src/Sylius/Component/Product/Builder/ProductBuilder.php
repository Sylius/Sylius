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

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

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
     * @var FactoryInterface
     */
    protected $productFactory;

    /**
     * @var ObjectManager
     */
    protected $productManager;

    /**
     * @var FactoryInterface
     */
    protected $attributeFactory;

    /**
     * @var RepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var FactoryInterface
     */
    protected $attributeValueFactory;

    public function __construct(
        FactoryInterface $productFactory,
        ObjectManager       $productManager,
        FactoryInterface $attributeFactory,
        RepositoryInterface $attributeRepository,
        FactoryInterface $attributeValueFactory
    ) {
        $this->productFactory = $productFactory;
        $this->productManager = $productManager;
        $this->attributeFactory = $attributeFactory;
        $this->attributeRepository = $attributeRepository;
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

            $this->productManager->persist($attribute);
            $this->productManager->flush($attribute);
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
    public function save($flush = true)
    {
        $this->productManager->persist($this->product);

        if ($flush) {
            $this->productManager->flush();
        }

        return $this->product;
    }
}
