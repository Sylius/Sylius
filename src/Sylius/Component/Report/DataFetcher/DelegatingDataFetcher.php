<?php
/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Component\Report\DataFetcher;

use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Report\Model\ReportInterface;

/**
* Data fetcher choice type
*
* @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
*/
class DelegatingDataFetcher implements DelegatingDataFetcherInterface
{
    /**
    * DataFetcher registry.
    *
    * @var ServiceRegistryInterface
    */
    protected $registry;

    /**
    * Constructor.
    *
    * @param ServiceRegistryInterface $registry
    */
    public function __construct(ServiceRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
    * {@inheritdoc}
    */
    public function fetch(ReportInterface $subject)
    {
        if (null === $type = $subject->getDataFetcher()) {
            throw new \InvalidArgumentException('Cannot fetch data for ReportInterface instance without DataFetcher defined.');
        }

        $dataFetcher = $this->registry->get($type);

        return $dataFetcher->fetch($subject->getDataFetcherConfiguration());
    }
}
