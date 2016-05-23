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

use Sylius\Component\Report\Model\ReportInterface;

/**
 * Delegating data fetcher.
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface DelegatingDataFetcherInterface
{
    /**
     * Fetch data for given config.
     *
     * @param ReportInterface $report
     * @param array           $configuration
     *
     * @return array
     */
    public function fetch(ReportInterface $report, array $configuration = []);
}
