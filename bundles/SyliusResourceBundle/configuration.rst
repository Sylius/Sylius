Configuration
=============

There are two ways to configure the resources used by this bundle. You can manage your configuration for all yours bundles (explained in Basic Configuration) or into yours bundles (explained in Advanced configuration).

Basic configuration
-------------------

In your `app/config.yml` (or in an imported configuration file), you need to define what resources you want to use :

.. code-block:: yaml

    sylius_resource:
        resources:
            app.user:
                driver: doctrine/orm
                templates: App:User
                classes:
                    model: App\Entity\User
                    interface: App\Entity\UserInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository

Advanced configuration
----------------------

You need to expose a semantic configuration for your bundle. The following example show you a basic `Configuration` that the resource bundle needs to work.

.. code-block:: php

    class Configuration implements ConfigurationInterface
    {
        public function getConfigTreeBuilder()
        {
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('bundle_name');

            $rootNode
                // Driver used by the resource bundle
                ->children()
                    ->scalarNode('driver')->isRequired()->cannotBeEmpty()->end()
                ->end()

                // Validation groups used by the form component
                ->children()
                    ->arrayNode('validation_groups')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('MyEntity')
                                ->prototype('scalar')->end()
                                ->defaultValue(array('your_group'))
                            ->end()
                        ->end()
                    ->end()
                ->end()

                // The resources
                ->children()
                    ->arrayNode('classes')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('MyEntity')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('model')->defaultValue('Sylius\Bundle\PromotionsBundle\Model\MyEntity')->end()
                                    ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                    ->scalarNode('repository')->end()
                                    ->scalarNode('form')->defaultValue('Sylius\Bundle\PromotionsBundle\Form\Type\MyformType')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ;

            return $treeBuilder;
        }
    }

The resource bundle provide you `AbstractResourceExtension`, your bundle extension have to extends it.

.. code-block:: php

    use Sylius\Bundle\ResourceBundle\DependencyInjection\AbstractResourceExtension;

    class MyBundleExtension extends AbstractResourceExtension
    {
        // You can choose your application name, it will use to prefix the configuration keys in the container.
        protected $applicationName = 'sylius';

        // You can define where yours service definitions are
        protected $configDirectory = '/../Resources/config';

        // You can define what service definitions you want to load
        protected $configFiles = array(
            'services',
            'forms',
        );

        public function load(array $config, ContainerBuilder $container)
        {
            $this->configure(
                $config,
                new Configuration(),
                $container,
                self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS
            );
        }
    }

The last parameter of the `AbstractResourceExtension::configure()` allows you to define what functionalities you want to use :

 * CONFIGURE_LOADER : load yours service definitions located in `$applicationName`
 * CONFIGURE_PARAMETERS : set to the container the configured resource classes using the pattern `appName.serviceType.resourceName.class`
   For example : `sylius.controller.product.class`. For a form, it is a bit different : 'sylius.form.type.product.class'
 * CONFIGURE_VALIDATORS : set to the container the configured validation groups using the pattern `appName.validation_group.modelName`
   For example `sylius.validation_group.product`
 * CONFIGURE_DATABASE : Load the database driver, available drivers are `doctrine/orm`, `doctrine/mongodb-odm` and `doctrine/phpcr-odm`

And now, your bundle is configurable like that :

.. code-block:: php

    sylius_product:
        driver: doctrine/orm
        validation_groups:
            product: [sylius]
        classes:
            product:
                model: Sylius\Bundle\CoreBundle\Model\Product
                controller: Sylius\Bundle\CoreBundle\Controller\ProductController
                repository: Sylius\Bundle\CoreBundle\Repository\ProductRepository
                form: Sylius\Bundle\CoreBundle\Form\Type\ProductType

And... we're done!

This configuration registers for you several services and service aliases.

First of all, it gives you **app.manager.user**, which is simple alias to a proper **ObjectManager** service.
For *doctrine/orm* it will be your default entity manager, and unless you want to stay completely storage agnostic, you can use
the entity (or document) manager the "usual way".

Secondly, you get an **app.repository.user**. It represents repository. This service by default has a custom class, which implements
``Sylius\\Bundle\\ResourceBundle\\Model\\RepositoryInterface`` (which extends the Doctrine **ObjectRepository**).

The last and most important service is **app.controller.user**, you'll learn about it in next section.
