<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\SyliusResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Money extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class SyliusMoneyExtension extends SyliusResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $this->configDir = __DIR__.'/../Resources/config';

        list($config) = $this->configure($config, new Configuration(), $container, self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS);

        $container->setParameter('sylius.money.locale', $config['locale']);
        $container->setParameter('sylius.money.currency', $config['currency']);
    }
}
