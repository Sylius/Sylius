<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SearchBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\SearchBundle\Model\SearchIndexInterface;
use Sylius\Bundle\SearchBundle\Model\SearchLogInterface;

/**
 * Search bundle.
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class SyliusSearchBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public static function getSupportedDrivers()
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return [
            SearchIndexInterface::class => 'sylius.model.search.class',
            SearchLogInterface::class => 'sylius.model.log.class',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Bundle\SearchBundle\Model';
    }
}
