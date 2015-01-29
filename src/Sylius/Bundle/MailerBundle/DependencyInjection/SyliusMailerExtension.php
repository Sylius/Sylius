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
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class SyliusMailerExtension extends AbstractResourceExtension
{
    protected $configFiles = array(
        'services',
    );

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $this->configure(
            $config,
            new Configuration(),
            $container,
            self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS | self::CONFIGURE_FORMS
        );

        $container->setAlias('sylius.email_sender.adapter', sprintf('sylius.email_sender.adapter.%s', $config[1]['adapter']));

        $container->setParameter('sylius.mailer.sender_name', $config[1]['sender']['name']);
        $container->setParameter('sylius.mailer.sender_address', $config[1]['sender']['address']);

        $templates = isset($config[1]['templates']) ? $config[1]['templates'] : array('SyliusMailerBundle::default.html.twig');

        $container->setParameter('sylius.mailer.emails', $config[1]['emails']);
        $container->setParameter('sylius.mailer.templates', $templates);
    }
}
