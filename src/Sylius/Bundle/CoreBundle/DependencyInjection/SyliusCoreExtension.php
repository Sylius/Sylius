<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\BaseExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Core extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusCoreExtension extends BaseExtension implements PrependExtensionInterface
{
    /**
     * @var array
     */
    private $bundles = array(
        'sylius_addressing',
        'sylius_inventory',
        'sylius_money',
        'sylius_payments',
        'sylius_payum',
        'sylius_product',
        'sylius_promotions',
        'sylius_order',
        'sylius_settings',
        'sylius_shipping',
        'sylius_taxation',
        'sylius_taxonomies',
    );

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $this->configDir = __DIR__.'/../Resources/config';

        list($config, $loader) = $this->configure($config, new Configuration(), $container, self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS);

        $loader->load('mailer.xml');

        if (!empty($config['order_confirmation'])) {
            $this->loadOrderConfirmation($config['order_confirmation'], $container, $loader, $config['from_email']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $container->getExtensionConfig($this->getAlias()));

        foreach ($container->getExtensions() as $name => $extension) {
            if (in_array($name, $this->bundles)) {
                $container->prependExtensionConfig($name, array('driver' => $config['driver']));
            }
        }
    }

    protected function loadOrderConfirmation(array $config, ContainerBuilder $container, XmlFileLoader $loader, array $fromEmail)
    {
        $loader->load('order_confirmation_mailer.xml');

        if ($config['enabled']) {
            $loader->load('order_confirmation_listener.xml');
        }

        if (isset($config['from_email'])) {
            // overwrite the global one
            $fromEmail = $config['from_email'];
            unset($config['from_email']);
        }

        $container->setParameter('sylius.email.order_confirmation.from_email', array($fromEmail['address'] => $fromEmail['sender_name']));
        $container->setParameter('sylius.email.order_confirmation.template', $config['template']);
    }
}
