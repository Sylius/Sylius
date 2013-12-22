<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\BaseExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Sylius promotions component extension.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class SyliusPromotionsExtension extends BaseExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $this->configDir = __DIR__.'/../Resources/config';

        $this->configure($config, new Configuration(), $container, self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS);
    }
}
