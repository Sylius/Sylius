<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxonomyBundle\Doctrine\ORM;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\TranslatableEntityRepository;
use Sylius\Component\Taxonomy\Model\TaxonomyInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Gedmo\Tool\Wrapper\EntityWrapper;
use Gedmo\Tree\Strategy\ORM\Nested;

/**
 * Base taxon repository.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class TaxonRepository extends TranslatableEntityRepository implements TaxonRepositoryInterface
{
    /**
     * Tree listener on event manager
     *
     * @var AbstractTreeListener $treeListener
     */
    protected $treeListener;

    public function __construct(EntityManager $em, ClassMetadata $class)
    {
        parent::__construct($em, $class);

        foreach ($em->getEventManager()->getListeners() as $listeners) {
            foreach ($listeners as $listener) {
                if ($listener instanceof \Gedmo\Tree\TreeListener) {
                    $this->treeListener = $listener;
                    break;
                }
            }
            if ($this->treeListener) {
                break;
            }
        }
    }

    public function getTaxonsAsList(TaxonomyInterface $taxonomy)
    {
        return $this->getQueryBuilder()
            ->where('o.taxonomy = :taxonomy')
            ->andWhere('o.parent IS NOT NULL')
            ->setParameter('taxonomy', $taxonomy)
            ->orderBy('o.left')
            ->getQuery()
            ->getResult()
        ;
    }
    
    /**
     * Moves a taxon object up for $number of elements.
     * This is inspired by Gedmo\Tree\Entity\Repository\NestedTreeRepository
     *
     * @param TaxonInterface $taxon
     * @param integer $number
     *
     * @return boolean
     */
    public function moveUp(TaxonInterface $taxon, $number = 1)
    {
        $meta = $this->getClassMetadata();
        $prevSiblings = array_reverse($this->getPrevSiblings($taxon));
        
        if ($numSiblings = count($prevSiblings)) {
            if ($number > $numSiblings) {
                $number = $numSiblings;
            }

            $this->treeListener
                ->getStrategy($this->_em, $meta->name)
                ->updateNode($this->_em, $taxon, $prevSiblings[$number-1], Nested::PREV_SIBLING);

            return true;
        }

        return false;
    }
    
    /**
     * Moves a taxon object down for $number of elements.
     * This is inspired by Gedmo\Tree\Entity\Repository\NestedTreeRepository
     *
     * @param TaxonInterface $taxon
     * @param integer $number
     *
     * @return boolean
     */
    public function moveDown(TaxonInterface $taxon, $number = 1)
    {
        $meta = $this->getClassMetadata();
        $nextSiblings = $this->getNextSiblings($taxon);

        if ($numSiblings = count($nextSiblings)) {
            if ($number > $numSiblings) {
                $number = $numSiblings;
            }

            $this->treeListener
                ->getStrategy($this->_em, $meta->name)
                ->updateNode($this->_em, $taxon, $nextSiblings[$number-1], Nested::NEXT_SIBLING);
            
            return true;
        }

        return false;
    }
    
    /**
     * Returns all siblings before a given taxon
     * This is inspired by Gedmo\Tree\Entity\Repository\NestedTreeRepository
     *
     * @param TaxonInterface $taxon
     *
     * @return array of TaxonInterface
     */
    private function getPrevSiblings(TaxonInterface $taxon)
    {
        $meta = $this->getClassMetadata();
        $wrapped = new EntityWrapper($taxon, $this->_em);
        
        $config = $this->treeListener->getConfiguration($this->_em, $meta->name);
        $parent = $wrapped->getPropertyValue($config['parent']);
        $left = $wrapped->getPropertyValue($config['left']);

        $qb = $this->_em->createQueryBuilder();
        $qb->select('taxon')
            ->from($config['useObjectClass'], 'taxon')
            ->where($qb->expr()->lt('taxon.'.$config['left'], $left))
            ->orderBy("taxon.{$config['left']}", 'ASC')
        ;

        $wrappedParent = new EntityWrapper($parent, $this->_em);
        $parentId = $wrappedParent->getIdentifier();
        $qb->andWhere($qb->expr()->eq('taxon.'.$config['parent'], is_string($parentId) ? $qb->expr()->literal($parentId) : $parentId));

        $query = $qb->getQuery();
        $result = $query->getResult();

        return $result;
    }
    
    /**
     * Returns all siblings after a given taxon
     * This is inspired by Gedmo\Tree\Entity\Repository\NestedTreeRepository
     *
     * @param TaxonInterface $taxon
     *
     * @return array of TaxonInterface
     */
    private function getNextSiblings(TaxonInterface $taxon)
    {
        $meta = $this->getClassMetadata();
        $wrapped = new EntityWrapper($taxon, $this->_em);

        $config = $this->treeListener->getConfiguration($this->_em, $meta->name);
        $parent = $wrapped->getPropertyValue($config['parent']);
        $left = $wrapped->getPropertyValue($config['left']);

        $qb = $this->_em->createQueryBuilder();
        $qb->select('taxon')
            ->from($config['useObjectClass'], 'taxon')
            ->where($qb->expr()->gt('taxon.'.$config['left'], $left))
            ->orderBy("taxon.{$config['left']}", 'ASC')
        ;
        
        $wrappedParent = new EntityWrapper($parent, $this->_em);
        $parentId = $wrappedParent->getIdentifier();
        $qb->andWhere($qb->expr()->eq('taxon.'.$config['parent'], is_string($parentId) ? $qb->expr()->literal($parentId) : $parentId));

        $query = $qb->getQuery();
        $result = $query->getResult();

        return $result;
    }
}
