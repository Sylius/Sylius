<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle;

use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\DoctrineSluggablePass;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\RoutingRepositoryPass;
use Sylius\Bundle\TranslationBundle\AbstractTranslationBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Sylius core bundle.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class SyliusCoreBundle extends AbstractTranslationBundle
{
    /**
     * {@inheritdoc}
     */
    public static function getSupportedDrivers()
    {
        return array(
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getSecurityRoles()
    {
        return array(
            'ROLE_SYLIUS_ADMIN'       => array(
                'ROLE_SYLIUS_USER_ADMIN',
                'ROLE_SYLIUS_GROUP_ADMIN',
            ),
            'ROLE_SYLIUS_USER_ADMIN'  => array(
                'ROLE_SYLIUS_USER_LIST',
                'ROLE_SYLIUS_USER_SHOW',
                'ROLE_SYLIUS_USER_CREATE',
                'ROLE_SYLIUS_USER_UPDATE',
                'ROLE_SYLIUS_USER_DELETE',
            ),
            'ROLE_SYLIUS_GROUP_ADMIN' => array(
                'ROLE_SYLIUS_GROUP_LIST',
                'ROLE_SYLIUS_GROUP_SHOW',
                'ROLE_SYLIUS_GROUP_CREATE',
                'ROLE_SYLIUS_GROUP_UPDATE',
                'ROLE_SYLIUS_GROUP_DELETE',
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DoctrineSluggablePass());
        $container->addCompilerPass(new RoutingRepositoryPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return array(
            'Sylius\Component\Core\Model\UserInterface'                => 'sylius.model.user.class',
            'Sylius\Component\Core\Model\UserOAuthInterface'           => 'sylius.model.user_oauth.class',
            'Sylius\Component\Core\Model\GroupInterface'               => 'sylius.model.group.class',
            'Sylius\Component\Core\Model\ProductVariantImageInterface' => 'sylius.model.product_variant_image.class',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Component\Core\Model';
    }
}
