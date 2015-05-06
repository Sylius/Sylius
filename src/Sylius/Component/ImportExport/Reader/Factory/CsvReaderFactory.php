<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\ImportExport\Reader\Factory;
use EasyCSV\Reader;

class CsvReaderFactory implements CsvReaderFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(array $configuration)
    {
        $csvReader = new Reader($configuration['file'], 'r', $configuration["headers"]);
        $csvReader->setDelimiter($configuration['delimiter']);
        $csvReader->setEnclosure($configuration['enclosure']);
    }
}
