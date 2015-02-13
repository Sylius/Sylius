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
        RepositoryInterface $shippingCategoryRepository,
        EntityManager $em)
    {
        $this->productRepository = $productRepository;
        $this->archetypeRepository = $archetypeRepository;
        $this->taxCategoryRepository = $taxCategoryRepository;
        $this->shippingCategoryRepository = $shippingCategoryRepository;
    }

    public function process($data)
    {
        if(!isset($data['sku'])){
            $this->logger->addError('Cannot import product without sku defined');
            $this->resultCode = 1;
            return null;
        }

        $product = $this->productRepository->findOneBySku($data['sku']);

        if (null !== $product && !$this->configuration['update']) {
            $this->logger->addInfo('Permision denied. Product sku was found, but update flag was not set');
            return null;
        }

        if (null === $product) {
            try {
                $product = $this->productRepository->createNew();
                $product->setSku($data['sku']);
            } catch (\Exception $e) {
                $this->logger->addInfo('Product cannot be created. Error message:'.$e->getMessage());
                $this->resultCode = 1;
                return null;
            }
        }

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
