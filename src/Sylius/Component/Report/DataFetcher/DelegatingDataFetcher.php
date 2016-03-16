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
     * @param ServiceRegistryInterface $registry
     */
    public function __construct(ServiceRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException If the report does not have a data fetcher.
     */
    public function fetch(ReportInterface $report, array $configuration = [])
    {
        if (null === $type = $report->getDataFetcher()) {
            throw new \InvalidArgumentException('Cannot fetch data for ReportInterface instance without DataFetcher defined.');
        }

        $dataFetcher = $this->registry->get($type);
        $configuration = empty($configuration) ? $report->getDataFetcherConfiguration() : $configuration;

        return $dataFetcher->fetch($configuration);
    }
}
