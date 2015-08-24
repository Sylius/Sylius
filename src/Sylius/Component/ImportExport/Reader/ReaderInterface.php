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

use Psr\Log\LoggerInterface;
use Sylius\Component\ImportExport\Model\JobInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ReaderInterface
{
    /**
     * Reads data based on given configuration.
     * Should log information about reading in logger.
     *
     * @param array           $configuration
     * @param LoggerInterface $logger
     *
     * @return array|null
     */
    public function read(array $configuration, LoggerInterface $logger);
    /**
     * Finalize job, add meta data to it.
     *
     * @param JobInterface $job
     */
    public function finalize(JobInterface $job);
    /**
     * Return code of error if any.
     *
     * = 0 - no error
     * > 0 - exception
     * < 0 - fatal error
     *
     * @return int
     */
    public function getResultCode();
    /**
     * Returns type of reader.
     *
     * @return string
     */
    public function getType();
}
