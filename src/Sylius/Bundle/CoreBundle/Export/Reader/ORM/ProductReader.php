<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Export\Reader\ORM;

use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Export user reader.
 *
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class ProductReader extends AbstractDoctrineReader
{
    private $productRepository;

    public function __construct(EntityRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    protected function getQuery()
    {
        $query = $this->productRepository->createQueryBuilder('p')
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

    public function process($product)
    {
        $archetype = $product->getArchetype();
        $taxCategory = $product->getTaxCategory();
        $shippingCategory = $product->getShippingCategory();
        $createdAt = (string) $product->getCreatedAt()->format('Y-m-d H:m:s');

        return array(
            'id'                => $product->getId(),
            'name'              => $product->getName(),
            'price'             => $product->getPrice(),
            'description'       => $product->getDescription(),
            'short_description' => $product->getShortDescription(),
            'archetype'         => $archetype ? $archetype->getCode() : null,
            'tax_category'      => $taxCategory ? $taxCategory->getName() : null,
            'shipping_category' => $shippingCategory ? $shippingCategory->getName() :null,
            'is_available_on'   => $product->isAvailable(),
            'meta_keywords'     => $product->getMetaKeywords(),
            'meta_description'  => $product->getMetaDescription(),
            'createdAt'         => $createdAt,
        );
    }
}
