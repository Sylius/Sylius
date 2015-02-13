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
 * Export product option reader.
 *
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class ProductOptionReader extends AbstractDoctrineReader
{
    private $productOptionRepository;
    
    public function __construct(RepositoryInterface $productOptionRepository)
    {
        $this->productOptionRepository = $productOptionRepository;
    }
    
    public function process($option)
    {
        $createdAt = (string) $option->getCreatedAt()->format('Y-m-d H:m:s');
        
        return array(
            'id'           => $option->getId(),
            'name'         => $option->getName(),
            'created_at'   => $createdAt,
            'presentation' => $option->getPresentation()
        );
    }
    
    public function getQuery()
    {
        $query = $this->productOptionRepository->createQueryBuilder('po')
            ->getQuery();
        
        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'product_option';
    }
}