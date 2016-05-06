Creating custom configuration source
====================================

If your needs can't be fulfilled by built-in configuration sources, you can create a custom one in a few minutes!

Configuration provider
----------------------

The configuration provider contains the core logic of themes configurations retrieval.

It requires only one method - ``getConfigurations()`` which receives no arguments and returns an array of configuration arrays.

.. code-block:: php

    use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationProviderInterface;

    final class CustomConfigurationProvider implements ConfigurationProviderInterface
    {
        /**
         * {@inheritdoc}
         */
        public function getConfigurations()
        {
            return [
                [
                    'name' => 'theme/name',
                    'path' => '/theme/path',
                    'title' => 'Theme title',
                ],
            ];
        }
    }

Configuration source factory
----------------------------

The configuration source factory is the glue between your brand new configuration provider and ThemeBundle.

It provides an easy way to allow customization of your configuration source and defines how the configuration
provider is constructed.

.. code-block:: php

    use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationSourceFactoryInterface;
    use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Symfony\Component\DependencyInjection\Definition;

    final class CustomConfigurationSourceFactory implements ConfigurationSourceFactoryInterface
    {
        /**
         * {@inheritdoc}
         */
        public function buildConfiguration(ArrayNodeDefinition $node)
        {
            $node
                ->children()
                    ->scalarNode('option')
            ;
        }

        /**
         * {@inheritdoc}
         */
        public function initializeSource(ContainerBuilder $container, array $config)
        {
            return new Definition(CustomConfigurationProvider::class, [
                $config['option'], // pass an argument configured by end user to configuration provider
            ]);
        }

        /**
         * {@inheritdoc}
         */
        public function getName()
        {
            return 'custom';
        }
    }

.. note::
    Try not to define any public services in the container inside ``initializeSource()`` - it will prevent Symfony from
    cleaning it up and will remain in the compiled container even if not used.

The last step is to tell ThemeBundle to use the source factory defined before. It can be done in your bundle definition:

.. code-block:: php

    use Sylius\Bundle\ThemeBundle\DependencyInjection\SyliusThemeExtension;
    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Symfony\Component\HttpKernel\Bundle\Bundle;

    /**
     * @author Kamil Kokot <kamil.kokot@lakion.com>
     */
    final class AcmeBundle extends Bundle
    {
        /**
         * {@inheritdoc}
         */
        public function build(ContainerBuilder $container)
        {
            /** @var SyliusThemeExtension $themeExtension */
            $themeExtension = $container->getExtension('sylius_theme');
            $themeExtension->addConfigurationSourceFactory(new CustomConfigurationSourceFactory());
        }
    }

Usage
-----

Configuration source is set up, it will start providing themes configurations as soon as it is enabled in ThemeBundle:

.. code-block:: yaml

    sylius_theme:
        sources:
            custom: ~
