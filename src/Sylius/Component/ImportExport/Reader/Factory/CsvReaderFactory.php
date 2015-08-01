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

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class CsvReaderFactory implements CsvReaderFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(array $configuration)
    {
        $this->validateConfiguration($configuration);

        $csvReader = new Reader($configuration['file'], 'r', $configuration["headers"]);
        $csvReader->setDelimiter($configuration['delimiter']);
        $csvReader->setEnclosure($configuration['enclosure']);

        return $csvReader;
    }

    /**
     * @param array $configuration
     *
     * @throw \InvalidArgumentException
     */
    private function validateConfiguration(array $configuration)
    {
        if (
            !isset($configuration['file']) ||
            !isset($configuration['headers']) ||
            !isset($configuration['delimiter']) ||
            !isset($configuration['enclosure'])
        ) {
            throw new \InvalidArgumentException('The fields: file, headers, delimiter, enclosure has to be set');
        }
    }
}
