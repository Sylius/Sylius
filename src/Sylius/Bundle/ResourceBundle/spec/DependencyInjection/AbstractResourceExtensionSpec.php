<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\DependencyInjection;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Aleksey Bannov <a.s.bannov@gmail.com>
 */
class AbstractResourceExtensionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf('spec\Sylius\Bundle\ResourceBundle\DependencyInjection\ConcreteResourceExtension');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension');
    }

    function it_should_not_create_definition_if_dont_configured(ContainerBuilder $container)
    {
        $this->mockDefaultBehavior($container);

        $this->configure(
            array(
                'sylius' => array(
                    'driver'  => SyliusResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM,
                    'classes' => array(
                        'resource' => array(
                            'form' => array(
                                'choice' => 'Sylius\ChoiceFormType'
                            )
                        )
                    )
                )
            ),
            new Configuration(),
            $container,
            AbstractResourceExtension::CONFIGURE_LOADER
        );
    }

    function it_should_create_choice_form_definition(ContainerBuilder $container)
    {
        $this->mockDefaultBehavior($container);

        $definition = new Definition('Sylius\ChoiceFormType');
        $definition
            ->setArguments(array(
                'Sylius\Model',
                SyliusResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM,
                'sylius_resource_choice'
            ))
            ->addTag('form.type', array('alias' => 'sylius_resource_choice'));
        $container
            ->setDefinition(
                'sylius.form.type.resource_choice',
                Argument::exact($definition)
            )
            ->shouldBeCalled();

        $this->configure(
            array(
                'sylius' => array(
                    'driver'  => SyliusResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM,
                    'classes' => array(
                        'resource' => array(
                            'form' => array(
                                'choice' => 'Sylius\ChoiceFormType'
                            )
                        )
                    )
                )
            ),
            new Configuration(),
            $container,
            AbstractResourceExtension::CONFIGURE_FORMS
        );
    }

    function it_should_create_single_form_definition(ContainerBuilder $container)
    {
        $this->mockDefaultBehavior($container);

        $container
            ->setDefinition(
                'sylius.form.type.resource',
                Argument::type('Symfony\Component\DependencyInjection\Definition')
            )
            ->shouldBeCalled();

        $this->configure(
            array(
                'sylius' => array(
                    'driver'  => SyliusResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM,
                    'classes' => array(
                        'resource' => array(
                            'form' => array(
                                AbstractResourceExtension::DEFAULT_KEY => 'Sylius\FormType'
                            )
                        )
                    ),
                )
            ),
            new Configuration(),
            $container,
            AbstractResourceExtension::CONFIGURE_FORMS
        );
    }

    function it_should_create_multiple_form_definition(ContainerBuilder $container)
    {
        $this->mockDefaultBehavior($container);

        $container->setDefinition(
            'sylius.form.type.resource',
            Argument::type('Symfony\Component\DependencyInjection\Definition')
        )
            ->shouldBeCalled();

        $container->setDefinition(
            'sylius.form.type.resource_other',
            Argument::type('Symfony\Component\DependencyInjection\Definition')
        )
            ->shouldBeCalled();

        $this->configure(
            array(
                'sylius' => array(
                    'driver'  => SyliusResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM,
                    'classes' => array(
                        'resource' => array(
                            'form' => array(
                                AbstractResourceExtension::DEFAULT_KEY => 'Sylius\FormType',
                                'other'                                => 'Sylius\OtherFormType',
                            )
                        )
                    ),
                )
            ),
            new Configuration(),
            $container,
            AbstractResourceExtension::CONFIGURE_FORMS
        );
    }

    protected function mockDefaultBehavior($container)
    {
        $container->hasParameter('sylius.config.classes')->willReturn(false);
        $container->setParameter('sylius.config.classes', Argument::any())->shouldBeCalled();
    }
}

class ConcreteResourceExtension extends AbstractResourceExtension
{
    protected $configFiles = array();

    protected $configDirectory = '/';
}

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('sylius');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('driver')->isRequired()->cannotBeEmpty()->end()
            ->end()
            ->children()
            ->arrayNode('classes')
            ->addDefaultsIfNotSet()
            ->children()
            ->arrayNode('resource')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('model')->defaultValue('Sylius\Model')->end()
            ->arrayNode('form')
            ->prototype('scalar')->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
