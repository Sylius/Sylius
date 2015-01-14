<?php

namespace Sylius\Component\Report\DataFetcher;

use Sylius\Component\Report\Model\ReportInterface;

/**
* Delegating data fetcher.
*
* @author Łukasz Chruściel <lchrusciel@gmail.com>
*/
interface DelegatingDataFetcherInterface
{       
    /**
    * Fetch data for given config.
    *
    * @param ReportInterface $subject
    * @param array $context
    *
    * @return array
    */
    public function fetch(ReportInterface $subject, array $context = array());
}