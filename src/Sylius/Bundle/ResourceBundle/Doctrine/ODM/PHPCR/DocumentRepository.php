<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR;

use Doctrine\ODM\PHPCR\DocumentRepository as BaseDocumentRepository;
use Doctrine\ODM\PHPCR\Query\Builder\QueryBuilder;
use Pagerfanta\Adapter\DoctrineODMPhpcrAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

@trigger_error(sprintf('The "%s" class is deprecated since Sylius 1.3. Doctrine MongoDB and PHPCR support will no longer be supported in Sylius 2.0.', DocumentRepository::class), E_USER_DEPRECATED);

/**
 * Doctrine PHPCR-ODM driver document repository.
 */
class DocumentRepository extends BaseDocumentRepository implements RepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createPaginator(array $criteria = [], array $sorting = []): iterable
    {
        $queryBuilder = $this->getCollectionQueryBuilder();

        $this->applyCriteria($queryBuilder, $criteria);
        $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
    }

    /**
     * {@inheritdoc}
     */
    public function add(ResourceInterface $resource): void
    {
        $this->dm->persist($resource);
        $this->dm->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function remove(ResourceInterface $resource): void
    {
        if (null !== $this->find($resource->getId())) {
            $this->dm->remove($resource);
            $this->dm->flush();
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     *
     * @return Pagerfanta
     */
    public function getPaginator(QueryBuilder $queryBuilder)
    {
        return new Pagerfanta(new DoctrineODMPhpcrAdapter($queryBuilder));
    }

    /**
     * @return QueryBuilder
     */
    protected function getCollectionQueryBuilder()
    {
        return $this->createQueryBuilder('o');
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $criteria
     */
    protected function applyCriteria(QueryBuilder $queryBuilder, array $criteria = [])
    {
        $metadata = $this->getClassMetadata();
        foreach ($criteria as $property => $value) {
            if (!empty($value)) {
                if ($property === $metadata->nodename) {
                    $queryBuilder
                        ->andWhere()
                            ->eq()
                                ->localName($this->getAlias())
                                ->literal($value);
                } else {
                    $queryBuilder
                        ->andWhere()
                            ->eq()
                                ->field($this->getPropertyName($property))
                                ->literal($value);
                }
            }
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $sorting
     */
    protected function applySorting(QueryBuilder $queryBuilder, array $sorting = [])
    {
        foreach ($sorting as $property => $order) {
            if (!empty($order)) {
                $queryBuilder->orderBy()->{$order}()->field('o.' . $property);
            }
        }

        $queryBuilder->end();
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getPropertyName($name)
    {
        if (false === strpos($name, '.')) {
            return $this->getAlias() . '.' . $name;
        }

        return $name;
    }

    /**
     * @return string
     */
    protected function getAlias()
    {
        return 'o';
    }
}
