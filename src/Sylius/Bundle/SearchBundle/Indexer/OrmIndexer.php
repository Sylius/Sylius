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

use Doctrine\DBAL\Schema\Index;
use Doctrine\ORM\EntityManager;
use FOS\ElasticaBundle\Transformer\ModelToElasticaAutoTransformer;
use Sylius\Bundle\SearchBundle\Model\SearchIndex;

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

    /**
     * @var
     */
    private $output;

    const SPACER = ' ';

    /**
     * @param array                          $config
     * @param ModelToElasticaAutoTransformer $transformer
     */
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
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param $message
     */
    private function addToOutput($message)
    {
        $this->output .= $message . PHP_EOL;
    }

    /**
     * {@inheritdoc}
     */
    public function populate(EntityManager $em = null)
    {
        $this->setEntityManager($em);

        $this->addToOutput('Reseting index');

        $connection = $em->getConnection();

        $sm = $connection->getSchemaManager();

        $tableIndexes = array_keys($sm->listTableIndexes('sylius_search_index'));

        if (in_array('fulltext_search_idx', array_values($tableIndexes))) {
            $sm->dropIndex('fulltext_search_idx', 'sylius_search_index');
        }

        $dbPlatform = $connection->getDatabasePlatform();
        $q = $dbPlatform->getTruncateTableSQL('sylius_search_index');
        $connection->executeUpdate($q);

        foreach ($this->config['orm_indexes'] as $index) {
            $this->createIndex($index['class'], $index['mappings']);
        }

        $index = new Index('fulltext_search_idx', array('value'), false, false, array('fulltext'));
        $sm->createIndex($index, 'sylius_search_index');

        return $this;
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

        $this->addToOutput('Populating index table with ' . $entity . ' data');

        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('u')
            ->from($entity, 'u');
        $results = $queryBuilder->getQuery()->getResult();

        foreach ($results as $element) {
            $this->createIndexForEntity($entity, $fields, $element);
        }

        $this->addToOutput('Index created successfully');
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
            ->from('Sylius\Bundle\SearchBundle\Model\SearchIndex', 'u')
            ->where('u.itemId = :item_id')
            ->andWhere('u.entity = :entity_namespace')
            ->setParameter(':item_id', $entity->getId())
            ->setParameter(':entity_namespace', get_class($entity));

        try {

            $searchIndex = $queryBuilder->getQuery()->getSingleResult();
            $searchIndex->setValue($content);

        }catch(\Doctrine\ORM\NoResultException $e) {

            $searchIndex = new SearchIndex();

            $searchIndex->setItemId($entity->getId());
            $searchIndex->setEntity($entityName);
            $searchIndex->setValue($content);
        }

        $this->getTagsForElementAndSave($entity, $searchIndex);
    }

    /**
     * @param $entity
     */
    public function removeIndexForEntity($entity)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->delete('Sylius\Bundle\SearchBundle\Model\SearchIndex', 'u')
            ->where('u.itemId = :item_id')
            ->andWhere('u.entity = :entity_namespace')
            ->setParameter(':item_id', $entity->getId())
            ->setParameter(':entity_namespace', get_class($entity));

        $result = $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param $element
     * @param $searchIndex
     */
    public function getTagsForElementAndSave($element, $searchIndex)
    {
        /*
         * We bound orm with elasticsearch at this point. I could separate the logic but this
         * means that we will have logic duplication. Maybe this could be refactored in the future.
         */
        $elasticaDocument = $this->transformer->transform($element, array_flip(array_keys($this->config['filters']['facets'])));
        $searchIndex->setTags(serialize($elasticaDocument->getData()));

        $this->em->persist($searchIndex);
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
        // TODO maybe I can use the property accessor here
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
    public function nestedValues($node)
    {
        if (is_array($node)) {
            $ret = '';
            foreach($node as $key => $val)
                $ret = $this->nestedValues($key, $val);
            return $ret;
        }
        return $node;
    }

}