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
 * Export product attribute reader.
 *
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class ProductArchetypeReader extends AbstractDoctrineReader
{
    private $productArchetypeRepository;
    
    public function __construct(RepositoryInterface $productArchetypeRepository)
    {
        $this->productArchetypeRepository = $productArchetypeRepository;
    }
    
    public function process($archetype)
    {
        $archetypes = array();
        $options = $archetype->getOptions()->toArray();
        $attributes = $archetype->getAttributes()->toArray();
        $createdAt = (string) $archetype->getCreatedAt()->format('Y-m-d H:m:s');
        
        $attributeName = implode("~", $attributes);
        $optionName = implode("~", $options);
        
        $archetypes = array_merge($archetypes, array(
            'id'         => $archetype->getId(),
            'code'       => $archetype->getCode(),
            'name'       => $archetype->getName(),
            'parent'     => $archetype->getParent(),
            'options'    => $optionName,
            'attributes' => $attributeName,
            'created_at' => $createdAt,
        ));
         
         return $archetypes;
    }
    
    public function getQuery()
    {
        $query = $this->productArchetypeRepository->createQueryBuilder('pac')
            ->getQuery();
        
        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'product_archetype';
    }
}