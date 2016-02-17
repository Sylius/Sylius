<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MetadataBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class SyliusMetadataExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $loader->load('services.xml');
        $loader->load('forms.xml');

        $container
            ->getDefinition('sylius.form.type.page_metadata')
            ->addArgument(new Reference('sylius.metadata.dynamic_form_choice_builder'))
        ;

        $this->addDynamicChoiceTagToForm($container, 'twitter', 'twitter_summary_card');
        $this->addDynamicChoiceTagToForm($container, 'twitter', 'twitter_summary_large_image_card');
        $this->addDynamicChoiceTagToForm($container, 'twitter', 'twitter_player_card');
        $this->addDynamicChoiceTagToForm($container, 'twitter', 'twitter_app_card');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        if (!$container->hasExtension('twig')) {
            return;
        }

        $container->prependExtensionConfig('twig', [
            'form_themes' => [
                'SyliusMetadataBundle:Form:dynamic_form_theme.html.twig',
            ],
        ]);
    }

    /**
     * @param ContainerBuilder $container
     * @param string $group
     * @param string $formName
     */
    private function addDynamicChoiceTagToForm(ContainerBuilder $container, $group, $formName)
    {
        $serviceName = 'sylius.form.type.'.$formName;

        if (!$container->hasDefinition($serviceName)) {
            throw new \InvalidArgumentException(sprintf(
                'Service "%s" was not found!',
                $serviceName
            ));
        }

        $formDefinition = $container->getDefinition($serviceName);
        $formDefinition->addTag('sylius.metadata.dynamic_form_choice', [
            'group' => $group,
            'label' => 'sylius.metadata.type.'.$formName,
            'class' => $formDefinition->getArgument(0),
        ]);
    }
}
