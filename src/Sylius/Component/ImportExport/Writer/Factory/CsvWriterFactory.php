<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\ImportExport\Writer\Factory;

use EasyCSV\Writer;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class CsvWriterFactory implements CsvWriterFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(array $configuration)
    {
        $this->validateConfiguration($configuration);

        $csvWriter = new Writer($configuration['file'], 'w');
        $csvWriter->setDelimiter($configuration['delimiter']);
        $csvWriter->setEnclosure($configuration['enclosure']);

        return $csvWriter;
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
            !isset($configuration['delimiter']) ||
            !isset($configuration['enclosure'])
        ) {
            throw new \InvalidArgumentException('The fields: file, delimiter, enclosure has to be set');
        }
    }
}
