<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class ObjectToIdentifierServicePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('doctrine')) {
            $definition = new DefinitionDecorator('sylius.form.type.object_to_identifier');
            $definition->addArgument(new Reference('doctrine'));
            $definition->addArgument('sylius_entity_to_identifier');
            $definition->addTag('form.type', array('alias' => 'sylius_entity_to_identifier'));

            $container->setDefinition('sylius_entity_to_identifier', $definition);

            $definition = $container->findDefinition('sylius.form.type.entity_hidden');
            $definition->replaceArgument(0, new Reference('doctrine'));
        }

        if ($container->hasDefinition('doctrine_mongodb')) {
            $definition = new DefinitionDecorator('sylius.form.type.object_to_identifier');
            $definition->addArgument(new Reference('doctrine_mongodb'));
            $definition->addArgument('sylius_document_to_identifier');
            $definition->addTag('form.type', array('alias' => 'sylius_document_to_identifier'));

            $container->setDefinition('sylius_document_to_identifier', $definition);

            if (!$container->hasDefinition('sylius_entity_to_identifier')) {
                $container->setAlias('sylius_entity_to_identifier', 'sylius_document_to_identifier');
            }

            if (!$container->hasDefinition('doctrine')) {
                $definition = $container->findDefinition('sylius.form.type.entity_hidden');
                $definition->replaceArgument(0, new Reference('doctrine_mongodb'));
            }
        }

        if ($container->hasDefinition('doctrine_phpcr')) {
            $definition = new DefinitionDecorator('sylius.form.type.object_to_identifier');
            $definition->addArgument(new Reference('doctrine_phpcr'));
            $definition->addArgument('sylius_phpcr_document_to_identifier');
            $definition->addTag('form.type', array('alias' => 'sylius_phpcr_document_to_identifier'));

            $container->setDefinition('sylius_phpcr_document_to_identifier', $definition);

            if (!$container->hasDefinition('sylius_entity_to_identifier')) {
                $container->setAlias('sylius_entity_to_identifier', 'sylius_phpcr_document_to_identifier');
            }
        }
    }
}
