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

use Psr\Log\LoggerInterface;
use Sylius\Component\ImportExport\Model\JobInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface WriterInterface
{
    /**
     * Writes $items with given $configuration.
     *
     * @param array           $rawUsers
     * @param array           $configuration
     * @param LoggerInterface $logger
     *
     * @return mixed
     */
    public function write(array $rawUsers, array $configuration, LoggerInterface $logger);

    /**
     * Finalize job, add meta data.
     *
     * @param JobInterface $job
     * @param array        $configuration
     */
    public function finalize(JobInterface $job, array $configuration);

    /**
     * Return code of error if any.
     * = 0 - no error
     * > 0 - exception
     * < 0 - fatal error.
     *
     * @return int
     */
    public function getResultCode();

    /**
     * @return string Type of data
     */
    public function getType();
}
