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
class SyliusReviewExtension extends AbstractResourceExtension
{
    /**
     * @var array
     */
    private $reviewSubjects = [];

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($config, $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->registerResources('sylius', $config['driver'], $this->resolveResources($config['resources'], $container), $container);

        foreach ($config['resources'] as $name => $parameters) {
            $this->addRequiredArgumentsToForms($name, $parameters, $container);
        }

        foreach ($this->reviewSubjects as $subject) {
            $this->addProperTagToReviewDeleteListener($subject, $container);
        }

        $configFiles = [
            'services.xml',
        ];

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }
    }

    /**
     * {@inheritdoc}
     */
    private function resolveResources(array $resources, ContainerBuilder $container)
    {
        $subjects = [];

        foreach ($resources as $subject => $parameters) {
            $this->reviewSubjects[] = $subject;
            $subjects[$subject] = $parameters;
        }

        $container->setParameter('sylius.review.subjects', $subjects);

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
     * @param string $name
     * @param array $parameters
     * @param ContainerBuilder $container
     */
    private function addRequiredArgumentsToForms($name, array $parameters, ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.form.type.'.$name.'_review')) {
            return;
        }

        foreach ($parameters['review']['classes']['form'] as $formName => $form) {
            $formKey = ('default' === $formName) ? $name.'_review' : $name.'_review_'.$formName;
            $formDefinition = $container->getDefinition('sylius.form.type.'.$formKey);
            $formDefinition->addArgument($name);
        }
    }

    /**
     * @param string $resourceName
     * @param ContainerBuilder $container
     */
    private function addProperTagToReviewDeleteListener($resourceName, ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.listener.review_delete')) {
            return;
        }

        $listenerDefinition = $container->getDefinition('sylius.listener.review_delete');
        $listenerDefinition->addTag('doctrine.event_listener', ['event' => 'postRemove', 'method' => 'recalculateSubjectRating']);
    }
}
