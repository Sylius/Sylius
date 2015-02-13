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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Product archetype writer.
 *
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class ProductArchetypeWriter extends AbstractDoctrineWriter
{
    private $productArchetypeRepository;
    private $productAttributeRepository;
    private $productOptionRepository;
    
    public function __construct(RepositoryInterface $productArchetypeRepository, RepositoryInterface $productAttributeRepository, RepositoryInterface $productOptionRepository, EntityManager $em)
    {
        parent::__construct($em);
        $this->productArchetypeRepository = $productArchetypeRepository;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->productOptionRepository = $productOptionRepository;
    }
    
    public function process($data) 
    {
        $productArchetypeRepository = $this->productArchetypeRepository;
        $options = explode("~", $data['options']);
        $attributes = explode("~", $data['attributes']);
        $parent = $productArchetypeRepository->findOneBy(array('name' => $data['parent']));
        
        if($productArchetype = $productArchetypeRepository->findOneBy(array('code' => $data['code']))) {
            $parent = $productArchetypeRepository->findOneBy(array('name' => $data['parent']));

            $data['name'] ? $productArchetype->setName($data['name']) : $productArchetype->getName();
            $data['code'] ? $productArchetype->setCode($data['code']) : $productArchetype->getCode();
            $data['parent'] ? $productArchetype->setParent($parent) : $productArchetype->getParent();
            $data['name'] ? $productArchetype->setCreatedAt(new \DateTime($data['created_at'])) : new \DateTime();

            foreach ($attributes as $attribute) {
                if ($productArchetype->hasAttribute($attribute)) {
                    $baseAttribute = $this->productAttributeRepository->findOneBy(array('name' => $attribute));
                    $productArchetype->addAttribute($baseAttribute);
                }
            }

            foreach ($options as $option) {
                if ($productArchetype->hasOption($option)) {
                    $baseOption = $this->productOptionRepository->findOneBy(array('name' => $option));
                    $productArchetype->addOption($baseOption);
                }
            }

            return $productArchetype;
        }
        
        $productArchetype = $productArchetypeRepository->createNew();
        
        $productArchetype->setName($data['name']);
        $productArchetype->setCode($data['code']);
        $productArchetype->setParent($parent);
        $productArchetype->setCreatedAt(new \DateTime($data['created_at']));
        
        foreach ($attributes as $attribute) {
            $baseAttribute = $this->productAttributeRepository->findOneBy(array('name' => $attribute));
            $productArchetype->addAttribute($baseAttribute);
        }
        
        foreach ($options as $option) {
            $baseOption = $this->productOptionRepository->findOneBy(array('name' => $option));
            $productArchetype->addOption($baseOption);
        }
        
        return $productArchetype;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'import_product_archetype';
    }
}