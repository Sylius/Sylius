<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\ImportExport\Writer;

use Monolog\Logger;
use Sylius\Component\ImportExport\Model\JobInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface WriterInterface
{
    /**
     * @param array $items
     */
    public function write(array $items);

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration, Logger $logger);

    /**
    *
    * @return Type of data
    */
    public function getType();
}
