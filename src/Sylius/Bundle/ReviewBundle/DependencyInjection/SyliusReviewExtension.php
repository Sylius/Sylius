<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReviewBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class SyliusReviewExtension extends AbstractResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($config, $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->registerResources('sylius', $config['driver'], $this->resolveResources($config['resources'], $container), $container);

        $loader->load('services.xml');

        $this->addProperTagToReviewDeleteListener($container);
    }

    /**
     * {@inheritdoc}
     */
    private function resolveResources(array $resources, ContainerBuilder $container)
    {
        $container->setParameter('sylius.review.subjects', $resources);

        $resolvedResources = [];
        foreach ($resources as $subjectName => $subjectConfig) {
            foreach ($subjectConfig as $resourceName => $resourceConfig) {
                if (is_array($resourceConfig)) {
                    $resolvedResources[$subjectName.'_'.$resourceName] = $resourceConfig;
                }
            }
        }

        return $resolvedResources;
    }

    /**
     * @param ContainerBuilder $container
     */
    private function addProperTagToReviewDeleteListener(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.listener.review_delete')) {
            return;
        }

        $listenerDefinition = $container->getDefinition('sylius.listener.review_delete');
        $listenerDefinition->addTag('doctrine.event_listener', ['event' => 'postRemove', 'method' => 'recalculateSubjectRating']);
    }
}
