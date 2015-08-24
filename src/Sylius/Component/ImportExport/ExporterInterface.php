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
 * @author Mateusz Zalewski <zaleslaw@.gmail.com>
 */
interface ExporterInterface
{
    /**
     * @param ExportProfileInterface $exportProfile
     * @param LoggerInterface        $logger
     */
    public function export(ExportProfileInterface $exportProfile, LoggerInterface $logger);
}
