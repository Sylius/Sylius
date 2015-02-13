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
use Doctrine\ORM\EntityManager;

/**
 * Product attribute writer.
 *
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class ProductAttributeWriter extends AbstractDoctrineWriter
{
    private $productAttributeRepository;
    
    public function __construct(RepositoryInterface $productAttributeRepository, EntityManager $em)
    {
        parent::__construct($em);
        $this->productAttributeRepository = $productAttributeRepository;
    }
    
    public function process($data) 
    {
        $productAttributeRepository = $this->productAttributeRepository;
        
        if($productAttributeRepository->findOneBy(array('name' => $data['name']))){
            $productAttribute = $productAttributeRepository->findOneByName($data['name']);
            
            $data['name'] ? $productAttribute->setName($data['name']) : $productAttribute->getName();
            $data['type'] ? $productAttribute->setType($data['type']) : $productAttribute->getType();
            $data['created_at'] ? $productAttribute->setCreatedAt(new \DateTime($data['created_at'])) : $productAttribute->getCreatedAt();
            $data['presentation'] ? $productAttribute->setPresentation($data['presentation']) : $productAttribute->getPresentation();

            return $productAttribute;
        }
        
        $productAttribute = $productAttributeRepository->createNew();
        
        $productAttribute->setName($data['name']);
        $productAttribute->setType($data['type']);
        $productAttribute->setCreatedAt(new \DateTime($data['created_at']));
        $productAttribute->setPresentation($data['presentation']);
        
        return $productAttribute;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'import_product_attribute';
    }
}