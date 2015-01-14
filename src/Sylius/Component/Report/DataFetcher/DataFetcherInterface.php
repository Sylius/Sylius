<?php

namespace Sylius\Component\Report\DataFetcher;

interface DataFetcherInterface
{
    /**
     * Fetching data from data base
     * 
     * @param array config
     * 
     * @return array of data
     */
    public fetch($config);

    /**
     * 
     * @return Type of data
     */
    public getType();
}