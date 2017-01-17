<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MailerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Jérémy Leherpeur <jeremy@leherpeur.net>
 */
final class SyliusMailerExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $configFiles = [
            'services.xml',
        ];

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }

        $container->setAlias('sylius.email_sender.adapter', $config['sender_adapter']);
        $container->setAlias('sylius.email_renderer.adapter', $config['renderer_adapter']);

        $container->setParameter('sylius.mailer.sender_name', $config['sender']['name']);
        $container->setParameter('sylius.mailer.sender_address', $config['sender']['address']);

        $templates = isset($config['templates']) ? $config['templates'] : ['Default' => 'SyliusMailerBundle::default.html.twig'];

        $container->setParameter('sylius.mailer.emails', $config['emails']);
        $container->setParameter('sylius.mailer.templates', $templates);
    }
}
