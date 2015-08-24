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
interface CsvWriterFactoryInterface
{
    /**
     * Creates Writer instance.
     *
     * @param array $configuration
     *
     * @return Writer
     */
    public function create(array $configuration);
}
