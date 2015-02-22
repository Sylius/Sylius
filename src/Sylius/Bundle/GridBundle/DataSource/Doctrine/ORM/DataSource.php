<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\DataSource\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Bundle\GridBundle\DataSource\Doctrine\ORM\ExpressionBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Grid\DataSource\DataSourceInterface;

/**
 * Doctrine DataSource.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DataSource implements DataSourceInterface
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var ExpressionBuilderInterface
     */
    private $expressionBuilder;

    /**
     * @param EntityRepository
     */
    function __construct(EntityRepository $repository, $method = null, $arguments = array())
    {
        if (null === $method) {
            $queryBuilder = $repository->createQueryBuilder('o');
        } else {
            $queryBuilder = call_user_func_array(array($repository, $method), $arguments);
        }

        $this->queryBuilder = $queryBuilder;
        $this->expressionBuilder = new ExpressionBuilder($queryBuilder);
    }

    /**
     * {@inheritdoc}
     */
    public function restrict($expression, $condition = DataSourceInterface::CONDITION_AND)
    {
        switch ($condition) {
            case DataSourceInterface::CONDITION_AND:
                $this->queryBuilder->andWhere($expression);
            break;
            case DataSourceInterface::CONDITION_OR:
                $this->queryBuilder->orWhere($expression);
            break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExpressionBuilder()
    {
        return $this->expressionBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return new Pagerfanta(new DoctrineORMAdapter($this->queryBuilder));
    }
}
