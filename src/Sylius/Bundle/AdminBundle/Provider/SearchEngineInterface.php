<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Provider;

use Sylius\Component\Search\Model\ResultSetInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

interface SearchEngineInterface
{
    /**
     * @param string $terms
     * @param ParameterBag $query
     *
     * @return ResultSetInterface[]
     */
    public function search(string $terms, ParameterBag $query): array;
}
