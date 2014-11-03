Configuring your resources
==========================

Now you need to configure your resources! It means that you will tell to this bundle what model, controller, repository, etc.
is used for each configured resource. It exists two ways for doing that, we will call them **Basic configuration** and
*Advanced configuration*. The first one is pretty easy because your just need to write configuration (in your config.yml, for example).
The second one allows you to embed configuration into your bundles but you will need to write some lines of code.
We will explain the both ways in the next chapters.

Basic configuration
-------------------

In your ``app/config.yml`` (or in an imported configuration file), you need to define what resources you want to use :

.. code-block:: yaml

    sylius_resource:
        resources:
            my_app.entity_key:
                driver: doctrine/orm
                manager: default
                templates: App:User
                classes:
                    model: MyApp\Entity\EntityName
                    interface: MyApp\Entity\EntityKeyInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository
            my_app.other_entity_key:
                driver: doctrine/odm
                manager: other_manager
                templates: App:User
                classes:
                    model: MyApp\Entity\OtherEntityKey
                    interface: MyApp\Document\OtherEntityKeyInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: Sylius\Bundle\ResourceBundle\Doctrine\ODM\DocumentRepository

At this step:

.. code-block:: bash

    $ php app/console container:debug | grep entity_key
    my_app.repository.entity_key   container MyApp\Entity\EntityName
    my_app.controller.entity_key   container Sylius\Bundle\ResourceBundle\Controller\ResourceController
    my_app.repository.entity_key   container Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository
    //...

    $ php app/console container:debug --parameters | grep entity_key
    sylius.config.classes        {"my_app.entity_key": {"driver":"...", "manager": "...", "classes":{"model":"...", "controller":"...", "repository":"...", "interface":"..."}}}
    //...

Advanced configuration
----------------------

First you must list the supported doctrine driver by your bundle, the available drivers are:

* ``SyliusResourceBundle::DRIVER_DOCTRINE_ORM``
* ``SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM``
* ``SyliusResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM``

.. code-block:: php

    class MyBundle extends AbstractResourceBundle
    {
        public static function getSupportedDrivers()
        {
            return array(
                SyliusResourceBundle::DRIVER_DOCTRINE_ORM
            );
        }
    }

.. note::

    Since the ``0.11`` your bundle class must implement ``ResourceBundleInterface``.

You need to expose a semantic configuration for your bundle. The following example show you a basic ``Configuration`` that the resource bundle needs to work.

.. code-block:: php

    class Configuration implements ConfigurationInterface
    {
        public function getConfigTreeBuilder()
        {
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('bundle_name');

            $rootNode
                // Driver used by the resource bundle
                ->scalarNode('driver')->isRequired()->cannotBeEmpty()->end()

                // Object manager used by the resource bundle, if not specified "default" will used
                ->scalarNode('manager')->defaultValue('default')->end()

                // Validation groups used by the form component
                ->arrayNode('validation_groups')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('MyEntity')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('your_group'))
                        ->end()
                    ->end()
                ->end()

                // Configure the template namespace used by each resource
                ->arrayNode('templates')
                ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('my_entity')->defaultValue('MyCoreBundle:Entity')->end()
                        ->scalarNode('my_other_entity')->defaultValue('MyOtherCoreBundle:Entity')->end()
                    ->end()
                ->end()


                // The resources
                ->arrayNode('classes')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('my_entity')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('MyApp\MyCustomBundle\Model\MyEntity')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                ->scalarNode('repository')->end()
                                ->scalarNode('form')->defaultValue('MyApp\MyCustomBundle\Form\Type\MyformType')->end()
                            ->end()
                        ->end()
                        ->arrayNode('my_other_entity')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('MyApp\MyCustomBundle\Model\MyOtherEntity')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                ->scalarNode('form')->defaultValue('MyApp\MyCustomBundle\Form\Type\MyformType')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ;

            return $treeBuilder;
        }
    }

The resource bundle provide you ``AbstractResourceExtension``, your bundle extension have to extends it.

.. code-block:: php

    use Sylius\Bundle\ResourceBundle\DependencyInjection\AbstractResourceExtension;

    class MyBundleExtension extends AbstractResourceExtension
    {
        // You can choose your application name, it will use to prefix the configuration keys in the container (the default value is sylius).
        protected $applicationName = 'my_app';

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

The last parameter of the ``AbstractResourceExtension::configure()`` allows you to define what functionalities you want to use :

 * CONFIGURE_LOADER : load yours service definitions located in ``$applicationName``
 * CONFIGURE_PARAMETERS : set to the container the configured resource classes using the pattern ``my_app.serviceType.resourceName.class``
   For example : ``sylius.controller.product.class``. For a form, it is a bit different : 'sylius.form.type.product.class'
 * CONFIGURE_VALIDATORS : set to the container the configured validation groups using the pattern ``my_app.validation_group.modelName``
   For example ``sylius.validation_group.product``
 * CONFIGURE_DATABASE : Load the database driver, available drivers are ``doctrine/orm``, ``doctrine/mongodb-odm`` and ``doctrine/phpcr-odm``

At this step:

.. code-block:: bash

    $ php app/console container:debug | grep my_entity
    my_app.controller.my_entity              container Sylius\Bundle\ResourceBundle\Controller\ResourceController
    my_app.form.type.my_entity               container MyApp\MyCustomBundle\Form\Type\TaxonomyType
    my_app.manager.my_entity                 n/a       alias for doctrine.orm.default_entity_manager
    my_app.repository.my_entity              container MyApp\MyCustomer\ModelRepository
    //...

    $ php app/console container:debug --parameters | grep my_entity
    my_app.config.classes                   {...}
    my_app.controller.my_entity.class       MyApp\MyCustomBundle\ModelController
    my_app.form.type.my_entity.class        MyApp\MyCustomBundle\FormType
    my_app.model.my_entity.class            MyApp\MyCustomBundle\Model
    my_app.repository.my_entity.class       MyApp\MyCustomBundle\ModelRepository
    my_app.validation_group.my_entity       ["my_app"]
    my_app_my_entity.driver                 doctrine/orm
    my_app_my_entity.driver.doctrine/orm    true
    //...

You can overwrite the configuration of your bundle like that :

.. code-block:: php

    bundle_name:
        driver: doctrine/orm
        manager: my_custom_manager
        validation_groups:
            product: [myCustomGroup]
        classes:
            my_entity:
                model: MyApp\MyOtherCustomBundle\Model
                controller: MyApp\MyOtherCustomBundle\Entity\ModelController
                repository: MyApp\MyOtherCustomBundle\Repository\ModelRepository
                form: MyApp\MyOtherCustomBundle\Form\Type\FormType

.. note::

    Caution: Your form is not declared as a service for now.

Combining the both configurations
---------------------------------

For now, with the advanced configuration you can not use several drivers but they can be overwritten. Example, you want to use
``doctrine/odm`` for ``my_other_entity`` (see previous chapter), you just need to add this extra configuration to the ``app/config.yml``.

.. code-block:: yaml

    sylius_resource:
        resources:
            my_app.other_entity_key:
                driver: doctrine/odm
                manager: my_custom_manager
                classes:
                    model: %my_app.model.my_entity.class%

And your manager will be overwrite:

.. code-block:: bash

    $ php app/console container:debug | grep my_app.manager.other_entity_key
    my_app.manager.other_entity_key       n/a       alias for doctrine.odm.my_custom_manager_document_manager

And... we're done!

