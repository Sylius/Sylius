<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
interface ResourceBundleInterface
{
    const MAPPING_XML = 'xml';
    const MAPPING_YAML = 'yml';
    const MAPPING_ANNOTATION = 'annotation';

    /**
     * Return an array which contains the supported drivers.
     *
     * @return array
     */
    public static function getSupportedDrivers();
}
