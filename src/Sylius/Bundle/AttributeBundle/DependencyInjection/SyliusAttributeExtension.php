<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class SyliusAttributeExtension extends AbstractResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($config, $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.xml');

        $this->registerResources('sylius', $config['driver'], $this->resolveResources($config['resources'], $container), $container);
        $this->resolveSubjectsConfigutaion($config['resources'], $container);
    }

    /**
     * @param array $resources
     * @param ContainerBuilder $container
     *
     * @return array
     */
    private function resolveResources(array $resources, ContainerBuilder $container)
    {
        $subjects = [];

        foreach ($resources as $subject => $parameters) {
            $subjects[$subject] = $parameters;
        }

        $container->setParameter('sylius.attribute.subjects', $subjects);

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
     * @param array $resources
     * @param ContainerBuilder $container
     */
    private function resolveSubjectsConfigutaion(array $resources, ContainerBuilder $container)
    {
        foreach ($resources as $subjectName => $subjectConfig) {
            foreach ($subjectConfig as $resourceName => $resourceConfig) {
                if (!is_array($resourceConfig)) {
                    continue;
                }

                $formDefinition = $container->getDefinition(sprintf('sylius.form.type.%s_%s', $subjectName, $resourceName));
                $formDefinition->addArgument($subjectName);

                if (isset($resourceConfig['translation'])) {
                    $formTranslationDefinition = $container
                        ->getDefinition(sprintf('sylius.form.type.%s_%s_translation', $subjectName, $resourceName))
                    ;
                    $formTranslationDefinition->addArgument($subjectName);
                }

                if (false !== strpos($resourceName, 'value')) {
                    $formDefinition->addArgument($container->getDefinition(sprintf('sylius.repository.%s_attribute', $subjectName)));
                }
            }
        }
    }
}
