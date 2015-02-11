<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\ImportExport\Reader;

use Monolog\Logger;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ReaderInterface
{
    /**
     * Reads data based on given configuration
     *
     * @return array
     */
    public function read();

    /**
     * Sets reader configuration
     *
     * @param array $configuration
     */
    public function setConfiguration(array $configuration, Logger $logger);

    /**
     * @return string
     */
    public function getType();
}
