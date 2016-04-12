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

use Sylius\Bundle\MailerBundle\Form\Type\EmailType;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Mailer\Model\Email;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle.
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Jérémy Leherpeur <jeremy@leherpeur.net>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_mailer');

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
                ->scalarNode('sender_adapter')->defaultValue('sylius.email_sender.adapter.swiftmailer')->end()
                ->scalarNode('renderer_adapter')->defaultValue('sylius.email_renderer.adapter.twig')->end()
            ->end()
        ;

        $this->addResourcesSection($rootNode);
        $this->addEmailsSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addResourcesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('email')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Email::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(EmailInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->arrayNode('form')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('default')->defaultValue(EmailType::class)->cannotBeEmpty()->end()
                                                ->scalarNode('choice')->defaultValue(ResourceChoiceType::class)->cannotBeEmpty()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode('validation_groups')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->arrayNode('default')
                                            ->prototype('scalar')->end()
                                            ->defaultValue(['sylius'])
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $node
     *
     * @return ArrayNodeDefinition
     */
    protected function addEmailsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('sender')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('name')->defaultValue('Example.com Store')->end()
                        ->scalarNode('address')->defaultValue('no-reply@example.com')->end()
                    ->end()
                ->end()
                ->arrayNode('emails')
                    ->useAttributeAsKey('code')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('subject')->cannotBeEmpty()->end()
                            ->scalarNode('template')->cannotBeEmpty()->end()
                            ->booleanNode('enabled')->defaultTrue()->end()
                            ->arrayNode('sender')
                                ->children()
                                    ->scalarNode('name')->end()
                                    ->scalarNode('address')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('templates')
                    ->useAttributeAsKey('name')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;
    }
}
