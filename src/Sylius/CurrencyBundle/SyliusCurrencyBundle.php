<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\CurrencyBundle;

use Sylius\ResourceBundle\AbstractResourceBundle;
use Sylius\ResourceBundle\SyliusResourceBundle;

/**
 * Currency bundle.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusCurrencyBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public function getSupportedDrivers()
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
            SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Currency\Model';
    }
}
