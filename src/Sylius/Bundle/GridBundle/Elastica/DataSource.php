<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\Elastica;

use Elastica\Query;
use Elastica\Query\Simple;
use Elastica\SearchableInterface;
use Elastica\Type;
use Pagerfanta\Adapter\ElasticaAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;
use Sylius\Component\Grid\Parameters;

/**
 * @author Nicolas Adler <nicolas.adler@openizi.com>
 */
class DataSource implements DataSourceInterface
{
    /**
     * @var Type
     */
    private $type;

    /**
     * @var ExpressionBuilderInterface
     */
    private $expressionBuilder;

    /**
     * @param SearchableInterface $type
     */
    function __construct(SearchableInterface $type, array $query = [])
    {
        $this->type = $type;
        $this->expressionBuilder = new ExpressionBuilder($query);
    }

    /**
     * {@inheritdoc}
     */
    public function restrict($expression, $condition = DataSourceInterface::CONDITION_AND)
    {
        $this->expressionBuilder->initQueryForFilters();

        $this->expressionBuilder->addFilter($expression, $condition);
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
    public function getData(Parameters $parameters)
    {
        $query = new Query(new Simple($this->expressionBuilder->getQuery()));
        $paginator = new Pagerfanta(new ElasticaAdapter($this->type, $query));
        $paginator->setNormalizeOutOfRangePages(true);
        $paginator->setCurrentPage($parameters->get('page', 1));

        return $paginator;
    }
}
