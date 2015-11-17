<?php

namespace Sylius\Bundle\CoreBundle\Purger;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class QueryLogger implements QueryLoggerInterface
{
    /**
     * @var array
     */
    private $queries = [];

    /**
     * {@inheritdoc}
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->queries[] = [
            'sql' => $sql,
            'params' => $params,
            'types' => $types,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function stopQuery()
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function getLoggedQueries()
    {
        return $this->queries;
    }

    /**
     * {@inheritdoc}
     */
    public function clearLoggedQueries()
    {
        $this->queries = [];
    }
}
