<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Import\Writer\ORM;

use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Product writer.
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ProductWriter extends AbstractDoctrineWriter
{
    /** 
     * @var RepositoryInterface
     */
    private $productRepository;
    /** 
     * @var RepositoryInterface
     */
    private $archetypeRepository;
    /** 
     * @var RepositoryInterface
     */
    private $taxCategoryRepository;
    /** 
     * @var RepositoryInterface
     */
    private $shippingCategoryRepository;
    
    public function __construct(
        RepositoryInterface $productRepository,
        RepositoryInterface $archetypeRepository,
        RepositoryInterface $taxCategoryRepository,
        RepositoryInterface $shippingCategoryRepository)
    {
        $this->productRepository = $productRepository;
        $this->archetypeRepository = $archetypeRepository;
        $this->taxCategoryRepository = $taxCategoryRepository;
        $this->shippingCategoryRepository = $shippingCategoryRepository;
    }
    
    public function process($data) 
    {
        $product = $this->productRepository->createNew();

        $archetype = $this->archetypeRepository->findOneByCode($data['archetype']);
        $taxCategory = $this->taxCategoryRepository->findOneByName($data['tax_category']);
        $shippingCategory = $this->shippingCategoryRepository->findOneByName($data['shipping_category']);

        $product->setName($data['name']);
        $product->setPrice($data['price']);
        $product->setDescription($data['description']);
        $product->setShortDescription($data['short_description']);
        $product->setArchetype($archetype);
        $product->setTaxCategory($taxCategory);
        $product->setShippingCategory($shippingCategory);
        $product->setIsAvailableOn($data['is_available_on']);
        $product->setMetaKeyWords($data['meta_keywords']);
        $product->setMetaDescription($data['meta_description']);
        $product->setCreatedAt(new \DateTime($data['createdAt']));

        var_dump($product);
        exit;

        return $product;
    }
    
    public function getQuery()
    {
        $query = $this->productRepository->createQueryBuilder('u')
            ->getQuery();
        
        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'product';
    }
}