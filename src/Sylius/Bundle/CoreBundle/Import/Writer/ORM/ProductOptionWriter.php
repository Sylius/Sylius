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
 * Product option writer.
 *
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class ProductOptionWriter extends AbstractDoctrineWriter
{
    private $productOptionRepository;
    
    public function __construct(RepositoryInterface $productOptionRepository, EntityManager $em)
    {
        parent::__construct($em);
        $this->productOptionRepository = $productOptionRepository;
    }
    
    public function process($data) 
    {
        $productOptionRepository = $this->productOptionRepository;
        
        if($productOptionRepository->findOneBy(array('name' => $data['name']))){
            $productOption = $productOptionRepository->findOneByName($data['name']);
            
            $data['name'] ? $productOption->setName($data['name']) : $productOption->getName();
            $data['created_at'] ? $productOption->setCreatedAt(new \DateTime($data['created_at'])) : $productOption->getCreatedAt();
            $data['presentation'] ? $productOption->setPresentation($data['presentation']) : $productOption->getPresentation();

            return $productOption;
        }
        
        $productOption = $productOptionRepository->createNew();
        
        $productOption->setName($data['name']);
        $productOption->setCreatedAt(new \DateTime($data['created_at']));
        $productOption->setPresentation($data['presentation']);
        
        return $productOption;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'import_product_option';
    }
}