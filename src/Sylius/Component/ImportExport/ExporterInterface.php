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

use Sylius\Component\ImportExport\Model\ExportProfileInterface;

/**
 * @author Mateusz Zalewski <zaleslaw@.gmail.com>
 */
interface ExporterInterface
{
    /**
     * Export data based on given export profile
     *
     * @param ExportProfileInterface $exportProfile
     */
    public function export(ExportProfileInterface $exportProfile);
}
