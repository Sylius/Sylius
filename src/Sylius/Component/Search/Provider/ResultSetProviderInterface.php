<?php

declare(strict_types=1);

namespace Sylius\Component\Search\Provider;

use Sylius\Component\Search\Model\ResultSetInterface;
use Sylius\Component\Search\Model\SearchQueryInterface;

interface ResultSetProviderInterface
{
    public function getResultSet(SearchQueryInterface $query): ResultSetInterface;
}
