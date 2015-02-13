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
 * Taxonomy writer.
 *
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class TaxonomyWriter extends AbstractDoctrineWriter
{
    private $taxonomyRepository;
    private $taxonRepository;
    
    public function __construct(RepositoryInterface $taxonomyRepository, RepositoryInterface $taxonRepository, EntityManager $em)
    {
        parent::__construct($em);
        $this->taxonomyRepository = $taxonomyRepository;
        $this->taxonRepository = $taxonRepository;
    }
    
    public function process($data) 
    {
        $taxonomyRepository = $this->taxonomyRepository;
        $taxonRepository = $this->taxonRepository;
        
        $taxon = $taxonRepository->createNew();
        $parent = $taxonRepository->findOneById($data['parent_id']);
        
        if($taxonomy = $taxonomyRepository->findOneBy(array('name' => $data['taxonomy_name']))){
            $root = $taxonRepository->findOneBy(array('name' => $data['root_name']));
            
            $data['taxonomy_name'] ? $taxonomy->setName($data['taxonomy_name']) : null;
            $data['taxonomy_name'] ? $taxon->setTaxonomy($taxonomy) : null;
            $data['root_name'] ? $root->setName($data['root_name']) : null;
            $data['root_slug'] ? $root->setSlug($data['root_slug']) : null;
            $data['root_permalink'] ? $root->setPermalink($data['root_permalink']) : null;
            $data['root_description'] ? $root->setDescription($data['root_description']) : null;
            $data['root_left_tree'] ? $root->setLeft($data['root_left_tree']) : null;
            $data['root_right_tree'] ? $root->setRight($data['root_right_tree']) : null;
            $taxonomy->setRoot($root);
                
            if($taxon = $taxonRepository->findOneBy(array('name' => $data['name'])))
            {
                $taxon->getName();
                $taxon->getSlug();
                $taxon->getPermalink(); 
                $taxon->getDescription(); 
                $taxon->getLeft(); 
                $taxon->getRight(); 
                $taxon->getLevel();
                $taxon->getParent();
                $taxonomy->addTaxon($taxon);
                
                return $taxonomy;
            }
            
            $data['name'] ? $taxon->setName($data['name']) : null;
            $data['slug'] ? $taxon->setSlug($data['slug']) : null;
            $data['permalink'] ? $taxon->setPermalink($data['permalink']) : null;
            $data['description'] ? $taxon->setDescription($data['description']) : null;
            $data['left_tree'] ? $taxon->setLeft($data['left_tree']) : null;
            $data['right_tree'] ? $taxon->setRight($data['right_tree']) : null;
            $data['tree_left'] ? $taxon->setLevel($data['tree_level']) : null;
            $data['parent_id'] ? $taxon->setParent($parent) : null;
            $taxonomy->addTaxon($taxon);
            
            return $taxonomy;
        }
        
        $taxonomy = $taxonomyRepository->createNew();
        $root = $taxonRepository->createNew();
        
        $taxonomy->setName($data['taxonomy_name']);
        $taxon->setTaxonomy($taxonomy);
        $data['root_name'] ? $root->setName($data['root_name']) : null;
        $data['root_slug'] ? $root->setSlug($data['root_slug']) : null;
        $data['root_permalink'] ? $root->setPermalink($data['root_permalink']) : null;
        $data['root_description'] ? $root->setDescription($data['root_description']) : null;
        $data['root_left_tree'] ? $root->setLeft($data['root_left_tree']) : null;
        $data['root_right_tree'] ? $root->setRight($data['root_right_tree']) : null;
        $taxonomy->setRoot($root);
        
        $data['name'] ? $taxon->setName($data['name']) : null;
        $data['slug'] ? $taxon->setSlug($data['slug']) : null;
        $data['permalink'] ? $taxon->setPermalink($data['permalink']) : null;
        $data['description'] ? $taxon->setDescription($data['description']) : null;
        $data['left_tree'] ? $taxon->setLeft($data['left_tree']) : null;
        $data['right_tree'] ? $taxon->setRight($data['right_tree']) : null;
        $data['tree_level'] ? $taxon->setLevel($data['tree_level']) : null;
        $data['parent_id'] ? $taxon->setParent($parent) : null;
        $taxonomy->addTaxon($taxon);
        
        return $taxonomy;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'import_product_option';
    }
}