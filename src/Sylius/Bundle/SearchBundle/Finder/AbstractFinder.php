<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SearchBundle\Finder;

use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductRepository;
use Sylius\Bundle\SearchBundle\Doctrine\ORM\SearchIndexRepository;
use Sylius\Bundle\SearchBundle\QueryLogger\QueryLoggerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;

abstract class AbstractFinder implements FinderInterface
{
    /**
     * @var SearchIndexRepository
     */
    protected $searchRepository;

    /**
     * @var
     */
    protected $config;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var QueryLoggerInterface
     */
    protected $queryLogger;

    /**
     * @var
     */
    protected $facets;

    /**
     * @var
     */
    protected $filters;

    /**
     * @var Pagerfanta
     */
    protected $paginator;

    /**
     * @var string
     */
    protected $facetGroup;

    /**
     * @var object
     */
    protected $targetIndex;

    /**
     * @var string[]
     */
    protected $targetTypes = [];

    /**
     * @var ChannelContextInterface
     */
    protected $channelContext;

    /**
     * @return Pagerfanta
     */
    public function getPaginator()
    {
        return $this->paginator;
    }

    /**
     * @return mixed
     */
    public function getFacets()
    {
        return $this->facets;
    }

    /**
     * @return mixed
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param string $targetType
     *
     * @return $this
     */
    public function addTargetType($targetType)
    {
        $this->targetTypes[] = $targetType;

        return $this;
    }

    /**
     * @param string $facetGroup
     *
     * @return $this
     */
    public function setFacetGroup($facetGroup)
    {
        $this->facetGroup = $facetGroup;

        return $this;
    }

    /**
     * @param object $targetIndex
     *
     * @return $this
     */
    public function setTargetIndex($targetIndex)
    {
        $this->targetIndex = $targetIndex;

        return $this;
    }
}
