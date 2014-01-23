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

use Sylius\Bundle\ResourceBundle\DependencyInjection\SyliusResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Core extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusCoreExtension extends SyliusResourceExtension implements PrependExtensionInterface
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

    private $emails = array(
        'order_confirmation',
        'customer_welcome'
    );

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $this->configDir = __DIR__.'/../Resources/config';

        list($config, $loader) = $this->configure($config, new Configuration(), $container, self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS);

        $loader->load('mailer/mailer.xml');

        $this->loadEmailsConfiguration($config['emails'], $container, $loader);
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

    /**
     * @param array            $config    The email section of the config for this bundle
     * @param ContainerBuilder $container
     * @param XmlFileLoader    $loader
     */
    protected function loadEmailsConfiguration(array $config, ContainerBuilder $container, XmlFileLoader $loader)
    {
        foreach ($this->emails as $emailType) {
            $loader->load('mailer/'.$emailType.'_mailer.xml');

            $fromEmail = isset($config[$emailType]['from_email']) ? $config[$emailType]['from_email'] : $config['from_email'];
            $container->setParameter(sprintf('sylius.email.%s.from_email', $emailType), array($fromEmail['address'] => $fromEmail['sender_name']));
            $container->setParameter(sprintf('sylius.email.%s.template', $emailType), $config[$emailType]['template']);

            if ($config['enabled'] && $config[$emailType]['enabled']) {
                $loader->load('mailer/'.$emailType.'_listener.xml');
            }
        }
    }
}
