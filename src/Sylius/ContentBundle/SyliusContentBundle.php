<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\ContentBundle;

use Sylius\ResourceBundle\AbstractResourceBundle;
use Sylius\ResourceBundle\SyliusResourceBundle;

/**
 * Sylius content bundle.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusContentBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public function getSupportedDrivers()
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM,
        ];
    }
}
