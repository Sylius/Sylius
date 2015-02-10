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

use Sylius\Bundle\ResourceBundle\DependencyInjection\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Mailer extension.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusMailerExtension extends AbstractResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        list($config) = $this->configure(
            $config,
            new Configuration(),
            $container,
            self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS | self::CONFIGURE_FORMS
        );

        $container->setAlias('sylius.email_sender.adapter', $config['adapter']);

        $container->setParameter('sylius.mailer.sender_name', $config['sender']['name']);
        $container->setParameter('sylius.mailer.sender_address', $config['sender']['address']);

        $templates = isset($config['templates']) ? $config['templates'] : array('Default' => 'SyliusMailerBundle::default.html.twig');

        $container->setParameter('sylius.mailer.emails', $config['emails']);
        $container->setParameter('sylius.mailer.templates', $templates);
    }
}
