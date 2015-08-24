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
use Sylius\Component\ImportExport\Model\ExportProfileInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class Exporter extends JobRunner implements ExporterInterface
{
    /**
     * {@inheritdoc}
     */
    public function export(ExportProfileInterface $exportProfile, LoggerInterface $logger)
    {
        $exportJob = $this->start($exportProfile, $logger);
        $jobStatus = $this->run($exportProfile, $logger, $exportJob);
        $this->end($exportJob, $logger, $jobStatus);
    }
}
