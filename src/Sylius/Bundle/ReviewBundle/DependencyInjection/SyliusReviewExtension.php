<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ReviewBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Bundle\ReviewBundle\EventListener\ReviewChangeListener;
use Sylius\Bundle\ReviewBundle\Updater\AverageRatingUpdater;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

final class SyliusReviewExtension extends AbstractResourceExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerResources('sylius', $config['driver'], $this->resolveResources($config['resources'], $container), $container);

        $loader->load('services.xml');

        $loader->load(sprintf('integrations/%s.xml', $config['driver']));
    }

    private function resolveResources(array $resources, ContainerBuilder $container): array
    {
        $container->setParameter('sylius.review.subjects', $resources);

        $this->createReviewListeners(array_keys($resources), $container);

        $resolvedResources = [];
        foreach ($resources as $subjectName => $subjectConfig) {
            foreach ($subjectConfig as $resourceName => $resourceConfig) {
                if (is_array($resourceConfig)) {
                    $resolvedResources[$subjectName . '_' . $resourceName] = $resourceConfig;
                }
            }
        }

        return $resolvedResources;
    }

    private function createReviewListeners(array $reviewSubjects, ContainerBuilder $container): void
    {
        foreach ($reviewSubjects as $reviewSubject) {
            $reviewChangeListener = new Definition(ReviewChangeListener::class, [
                new Reference(sprintf('sylius.%s_review.average_rating_updater', $reviewSubject)),
            ]);

            $reviewChangeListener
                ->setPublic(true)
                ->addTag('doctrine.event_listener', [
                    'event' => 'postPersist',
                    'lazy' => true,
                ])
                ->addTag('doctrine.event_listener', [
                    'event' => 'postUpdate',
                    'lazy' => true,
                ])
                ->addTag('doctrine.event_listener', [
                    'event' => 'preRemove',
                    'lazy' => true,
                ])
            ;

            $container->addDefinitions([
                sprintf('sylius.%s_review.average_rating_updater', $reviewSubject) => (new Definition(AverageRatingUpdater::class, [
                    new Reference('sylius.average_rating_calculator'),
                    new Reference(sprintf('sylius.manager.%s_review', $reviewSubject)),
                ]))->setPublic(true),
                sprintf('sylius.listener.%s_review_change', $reviewSubject) => $reviewChangeListener,
            ]);
        }
    }
}
