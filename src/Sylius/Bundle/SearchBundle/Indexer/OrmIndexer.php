<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SearchBundle\Indexer;

use Doctrine\ORM\EntityManager;
use FOS\ElasticaBundle\Transformer\ModelToElasticaAutoTransformer;
use Sylius\Bundle\SearchBundle\Entity\SyliusSearchIndex;

/**
 * Orm Indexer
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class OrmIndexer implements IndexerInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var
     */
    private $em;

    /**
     * @var
     */
    protected $transformer;

    const SPACER = ' ';

    public function __construct(Array $config, ModelToElasticaAutoTransformer $transformer)
    {
        $this->config      = $config;
        $this->transformer = $transformer;
    }

    /**
     * @param EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function populate(EntityManager $em = null)
    {
        $this->setEntityManager($em);

        echo 'Reseting index' . PHP_EOL;

        $sql  = 'show index from sylius_search_index';
        $stmt = $this->em->getConnection()
            ->prepare($sql);
        $stmt->execute();

        $res = $stmt->fetchAll(\PDO::FETCH_COLUMN, 2);

        if (in_array('fulltext_search_idx', array_values($res))) {
            $sql  = 'alter table sylius_search_index drop key fulltext_search_idx';
            $stmt = $this->em->getConnection()
                ->prepare($sql);
            $stmt->execute();
        }

        $sql  = 'truncate sylius_search_index';
        $stmt = $this->em->getConnection()
            ->prepare($sql);
        $stmt->execute();

        foreach ($this->config['orm_indexes'] as $index) {
            $this->createIndex($index['class'], $index['mappings']);
        }

        $sql  = 'create fulltext index fulltext_search_idx on sylius_search_index (value)';
        $stmt = $this->em->getConnection()
            ->prepare($sql);
        $stmt->execute();
    }

    /**
     * @param $entity
     * @param $fields
     *
     * @internal param $table
     */
    private function createIndex($entity, $fields)
    {

        $a = array_keys($fields);
        foreach ($a as &$value) {
            $value = 'u.' . $value;
        }

        echo 'Populating index table with ' . $entity . ' data' . PHP_EOL;

        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('u')
            ->from($entity, 'u')
            ->where('u.deletedAt IS NULL');
        $results = $queryBuilder->getQuery()->getResult();

        foreach ($results as $element) {
            $this->createIndexForEntity($entity, $fields, $element);
        }

        echo 'Index created successfully' . PHP_EOL;

    }

    /**
     * @param array $entities
     */
    public function insertMany(array $entities)
    {
        foreach($entities as $entity) {
            $class = get_class($entity);

            $indexName = $this->nestedValues($this->config['orm_indexes'], $class);

            $this->createIndexForEntity($class, $this->config['orm_indexes'][$indexName]['mappings'], $entity);
        }
    }

    /**
     * @param array $entities
     */
    public function removeMany(array $entities)
    {
        foreach($entities as $entity) {
            $this->removeIndexForEntity($entity);
        }
    }

    /**
     * @param $entityName
     * @param $fields
     * @param $entity
     */
    public function createIndexForEntity($entityName, $fields, $entity)
    {
        $content = $this->compileSearchableContent($fields, $entity);

        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('u')
            ->from('Sylius\Bundle\SearchBundle\Entity\SyliusSearchIndex', 'u')
            ->where('u.itemId = :item_id')
            ->andWhere('u.entity = :entity_namespace')
            ->setParameter(':item_id', $entity->getId())
            ->setParameter(':entity_namespace', get_class($entity));

        try {

            $syliusSearchIndex = $queryBuilder->getQuery()->getSingleResult();
            $syliusSearchIndex->setValue($content);

        }catch(\Doctrine\ORM\NoResultException $e) {

            $syliusSearchIndex = new SyliusSearchIndex();

            $syliusSearchIndex->setItemId($entity->getId());
            $syliusSearchIndex->setEntity($entityName);
            $syliusSearchIndex->setValue($content);
        }

        $this->getTagsForElementAndSave($entity, $syliusSearchIndex);
    }

    /**
     * @param $entity
     */
    public function removeIndexForEntity($entity)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->delete('Sylius\Bundle\SearchBundle\Entity\SyliusSearchIndex', 'u')
            ->where('u.itemId = :item_id')
            ->andWhere('u.entity = :entity_namespace')
            ->setParameter(':item_id', $entity->getId())
            ->setParameter(':entity_namespace', get_class($entity));

        $result = $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param $element
     * @param $syliusSearchIndex
     */
    public function getTagsForElementAndSave($element, $syliusSearchIndex)
    {
        /*
         * We bound orm with elasticsearch at this point. I could separate the logic but this
         * means that we will have logic duplication. Maybe this could be refactored in the future.
         */
        $elasticaDocument = $this->transformer->transform($element, array_flip(array_keys($this->config['filters']['facets'])));
        $syliusSearchIndex->setTags(serialize($elasticaDocument->getData()));

        $this->em->persist($syliusSearchIndex);
        $this->em->flush();
    }

    /**
     * @param $fields
     * @param $element
     *
     * @return string
     */
    public function compileSearchableContent($fields, $element)
    {
        $content = '';
        foreach (array_keys(array_slice($fields, 1)) as $field) {
            $func = 'get' . ucfirst($field);
            $content .= $element->$func() . self::SPACER;
        }

        return $content;
    }

    /**
     * Checks if the object is indexable or not.
     *
     * @param object $object
     * @return bool
     */
    public function isObjectIndexable($object)
    {
        foreach ($this->config['orm_indexes'] as $index) {
            if ($index['class'] == get_class($object) && $this->config['driver'] == 'orm') {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $node
     *
     * @return string
     */
    public function nestedValues($node) {
        if (is_array($node)) {
            $ret = '';
            foreach($node as $key => $val)
                $ret = $this->nestedValues($key, $val);
            return $ret;
        }
        return $node;
    }

}