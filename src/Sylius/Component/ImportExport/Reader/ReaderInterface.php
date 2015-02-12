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
use Sylius\Component\ImportExport\Model\JobInterface;

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
     * Finalize job, add meta data
     * 
     * @param JobInterface $job
     */
    public function finalize(JobInterface $job);

    /**
     * Return code of error if any
     * = 0 - no error
     * > 0 - exception
     * < 0 - fatal error
     * 
     * @return int
     */
    public function getResultCode();

    /**
     * Returns type of reader
     *
     * @return string
     */
    public function getType();
}
