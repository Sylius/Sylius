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

use Sylius\Component\ImportExport\Reader\ReaderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Core\Model\TaxonInterface;

/**
 * Export taxonomy reader.
 *
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class TaxonomyReader implements ReaderInterface {

    private $taxonomyRepository;
    private $results;
    private $configuration;
    private $running = false;

    public function __construct(RepositoryInterface $taxonomyRepository) 
    {
        $this->taxonomyRepository = $taxonomyRepository;
    }

    public function read() 
    {
        $taxons = array();
       
        if (!$this->running) {
            $this->running = true;
            $this->results = $this->getQuery()->execute();
            $this->results = new \ArrayIterator($this->results);
            $batchSize = $this->configuration['batch_size'];
        }
        
        for ($i = 0; $i < $batchSize; $i++) {
            if ($result = $this->results->current()) {
                $this->results->next();
            }
            $taxons = array_merge($taxons, $this->getChildren($result->getRoot()));
        }
        
        foreach ($taxons as $taxon) {
            $results[] = array(
                'taxonomy_id'      => $taxon->getTaxonomy()->getId(),
                'taxonomy_name'    => $taxon->getTaxonomy(),
                'root_id'          => $taxon->getTaxonomy()->getRoot()->getId(),
                'root_name'        => $taxon->getTaxonomy()->getRoot()->getName(),
                'root_slug'        => $taxon->getTaxonomy()->getRoot()->getSlug(),
                'root_permalink'   => $taxon->getTaxonomy()->getRoot()->getPermalink(),
                'root_description' => $taxon->getTaxonomy()->getRoot()->getDescription(),
                'root_left_tree'   => $taxon->getTaxonomy()->getRoot()->getLeft(),
                'root_right_tree'  => $taxon->getTaxonomy()->getRoot()->getRight(),
                'root_tree_level'  => $taxon->getTaxonomy()->getRoot()->getLevel(),
                'id'               => $taxon->getId(),
                'name'             => $taxon->getName(),
                'slug'             => $taxon->getSlug(),
                'permalink'        => $taxon->getPermalink(),
                'description'      => $taxon->getDescription(),
                'left_tree'        => $taxon->getLeft(),
                'right_tree'       => $taxon->getRight(),
                'tree_level'       => $taxon->getLevel(),
                'parent_id'        => $taxon->getParent()->getId(),
                'parent_name'      => $taxon->getParent()->getName(),
            );
        }
           
        return $results;
    }

    public function getChildren(TaxonInterface $taxon)
    {
        $children = $taxon->getChildren()->toArray();
        
        foreach ($children as $child) {
            $children = array_merge($children, $this->getChildren($child));
        }

        return $children;  
    }

    public function getQuery() 
    {
        $query = $this->taxonomyRepository->createQueryBuilder('t')
                ->getQuery();

        return $query;
    }

    public function setConfiguration(array $configuration) 
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getType() 
    {
        return 'taxonomy';
    }

}
