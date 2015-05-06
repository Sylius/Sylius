<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\ImportExport;

use Psr\Log\LoggerInterface;
use Sylius\Component\ImportExport\Model\ImportProfileInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class Importer extends JobRunner implements ImporterInterface
{
    /**
     * {@inheritdoc}
     */
    public function import(ImportProfileInterface $importProfile, LoggerInterface $logger)
    {
        $importJob = $this->start($importProfile, $logger);
        $jobStatus = $this->run($importProfile, $logger, $importJob);
        $this->end($importJob, $logger, $jobStatus);
    }
}
