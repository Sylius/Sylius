<?php

namespace Sylius\Bundle\CoreBundle\Purger;

use Doctrine\DBAL\Logging\SQLLogger;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class QueryLogger implements SQLLogger
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
     * @return array
     */
    public function getLoggedQueries()
    {
        return $this->queries;
    }

    public function clearLoggedQueries()
    {
        $this->queries = [];
    }
}
