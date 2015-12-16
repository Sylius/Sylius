<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class SyliusSettingsExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    /**
     * @var array
     */
    protected $configFiles = array(
        'services.xml',
        'templating.xml',
        'twig.xml',
    );

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->configure(
            $config,
            new Configuration(),
            $container,
            self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE
        );

        foreach ($config['resources'] as $resource => $classesGroups) {
            if (isset($classesGroups['classes']['model'])) {
                $container->setParameter('sylius.model.parameter.class', $classesGroups['classes']['model']);
            }

            if (isset($classesGroups['classes']['repository'])) {
                $container->setParameter('sylius.repository.parameter.class', $classesGroups['classes']['repository']);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        if (!$container->hasExtension('doctrine_cache')) {
            throw new \RuntimeException('DoctrineCacheBundle must be registered!');
        }
        
        if (!$container->hasParameter('sylius.cache')) {
            $container->setParameter('sylius.cache', array('type' => 'file_system'));
        }

        $container->prependExtensionConfig('doctrine_cache', array(
            'providers' => array(
                'sylius_settings' => '%sylius.cache%'
            )
        ));
    }
}
