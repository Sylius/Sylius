<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ContentBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

/**
 * Sylius content bundle.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusContentBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public static function getSupportedDrivers()
    {
        return array(
            SyliusResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM,
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getSecurityRoles()
    {
        return array(
            'ROLE_SYLIUS_ADMIN'         => array(
                'ROLE_SYLIUS_STATIC_CONTENT_ADMIN',
                'ROLE_SYLIUS_MENU_ADMIN',
                'ROLE_SYLIUS_ROUTE_LIST',
                'ROLE_SYLIUS_IMAGINE_BLOCK_ADMIN',
                'ROLE_SYLIUS_SIMPLE_BLOCK_ADMIN',
                'ROLE_SYLIUS_SLIDESHOW_BLOCK_ADMIN',
                'ROLE_SYLIUS_STRING_BLOCK_ADMIN',
            ),
            'ROLE_SYLIUS_STATIC_CONTENT_ADMIN' => array(
                'ROLE_SYLIUS_STATIC_CONTENT_LIST',
                'ROLE_SYLIUS_STATIC_CONTENT_SHOW',
                'ROLE_SYLIUS_STATIC_CONTENT_CREATE',
                'ROLE_SYLIUS_STATIC_CONTENT_UPDATE',
                'ROLE_SYLIUS_STATIC_CONTENT_DELETE',
            ),
            'ROLE_SYLIUS_MENU_ADMIN' => array(
                'ROLE_SYLIUS_MENU_LIST',
                'ROLE_SYLIUS_MENU_SHOW',
                'ROLE_SYLIUS_MENU_CREATE',
                'ROLE_SYLIUS_MENU_UPDATE',
                'ROLE_SYLIUS_MENU_DELETE',
            ),
            'ROLE_SYLIUS_ROUTE_LIST' => array(
                'ROLE_SYLIUS_ROUTE_LIST',
                'ROLE_SYLIUS_ROUTE_SHOW',
                'ROLE_SYLIUS_ROUTE_CREATE',
                'ROLE_SYLIUS_ROUTE_UPDATE',
                'ROLE_SYLIUS_ROUTE_DELETE',
            ),
            'ROLE_SYLIUS_IMAGINE_BLOCK_ADMIN' => array(
                'ROLE_SYLIUS_IMAGINE_BLOCK_LIST',
                'ROLE_SYLIUS_IMAGINE_BLOCK_SHOW',
                'ROLE_SYLIUS_IMAGINE_BLOCK_CREATE',
                'ROLE_SYLIUS_IMAGINE_BLOCK_UPDATE',
                'ROLE_SYLIUS_IMAGINE_BLOCK_DELETE',
            ),
            'ROLE_SYLIUS_SIMPLE_BLOCK_ADMIN' => array(
                'ROLE_SYLIUS_SIMPLE_BLOCK_LIST',
                'ROLE_SYLIUS_SIMPLE_BLOCK_SHOW',
                'ROLE_SYLIUS_SIMPLE_BLOCK_CREATE',
                'ROLE_SYLIUS_SIMPLE_BLOCK_UPDATE',
                'ROLE_SYLIUS_SIMPLE_BLOCK_DELETE',
            ),
            'ROLE_SYLIUS_SLIDESHOW_BLOCK_ADMIN' => array(
                'ROLE_SYLIUS_SLIDESHOW_BLOCK_LIST',
                'ROLE_SYLIUS_SLIDESHOW_BLOCK_SHOW',
                'ROLE_SYLIUS_SLIDESHOW_BLOCK_CREATE',
                'ROLE_SYLIUS_SLIDESHOW_BLOCK_UPDATE',
                'ROLE_SYLIUS_SLIDESHOW_BLOCK_DELETE',
            ),
            'ROLE_SYLIUS_STRING_BLOCK_ADMIN' => array(
                'ROLE_SYLIUS_STRING_BLOCK_LIST',
                'ROLE_SYLIUS_STRING_BLOCK_SHOW',
                'ROLE_SYLIUS_STRING_BLOCK_CREATE',
                'ROLE_SYLIUS_STRING_BLOCK_UPDATE',
                'ROLE_SYLIUS_STRING_BLOCK_DELETE',
            ),
        );
    }
}
