<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Loader;

use Symfony\Component\DependencyInjection\Loader\XmlFileLoader as BaseLoader;

/**
 * @author Arnaud Langlade <arn0d.dev@gamil.com>
 */
class XmlFileLoader extends BaseLoader
{
    /**
     * @param string[] $files
     */
    public function loadFiles(array $files)
    {
        foreach ($files as $file) {
            $this->load(sprintf('%s.xml', $file));
        }
    }

    /**
     * @param string[] $drivers
     */
    public function loadDriver(array $drivers)
    {
        foreach ($drivers as $driver) {
            try {
                $this->load(sprintf('driver/%s.xml', $driver));
            } catch (\Exception $e) {
            }
        }
    }
} 