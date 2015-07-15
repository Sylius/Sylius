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

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface DataFetcherInterface
{
    /**
     * Fetching data from data base
     *
     * @param array $configuration
     *
     * @return Data $data
     */
    public function fetch(array $configuration);

    /**
     * @return Type of data
     */
    public function getType();
}
