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
     * @param array $configuration
     *
     * @return Data $data
     */
    public function fetch(array $configuration);

    /**
     * @return string
     */
    public function getType();
}
