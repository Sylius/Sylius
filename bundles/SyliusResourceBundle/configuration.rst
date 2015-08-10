Configuring your resources
==========================

Now you need to configure your resources! It means that you will tell to this bundle which model, controller, repository, etc.
will be used for each configured resources. It exists two ways for doing that, we will call them **Basic configuration** and
*Advanced configuration*. You need to use the first one if you want to build a simple application. It is pretty easy because you just
need to write configuration (in your config.yml, for example). The second one allows you to embed configuration into your bundles
but you will need to write some extra lines of code. We will explain the both ways in the next chapters.

Basic configuration
-------------------

In your ``app/config.yml`` (or in an imported configuration file), you need to define which resources you want to use :

.. code-block:: yaml

    sylius_resource:
        resources:
            my_app.entity_name:
                driver: doctrine/orm
                object_manager: default
                templates: App:User
                classes:
                    model: MyApp\Entity\EntityName
                    interface: MyApp\Entity\EntityNameInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository
            my_app.document_name:
                driver: doctrine/mongodb-odm
                object_manager: default
                templates: App:User
                classes:
                    model: MyApp\Document\DocumentName
                    interface: MyApp\Document\DocumentNameInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: Sylius\Bundle\ResourceBundle\Doctrine\ODM\DocumentRepository

At this step, we can see what happen in the container:

.. code-block:: bash
    // For each resource, we declare a controller, a manager and a repository as a service
    $ php app/console container:debug | grep entity_key
    my_app.manager.entity_key      alias for "doctrine.orm.default_entity_manager"
    my_app.controller.entity_key   container Sylius\Bundle\ResourceBundle\Controller\ResourceController
    my_app.repository.entity_key   container Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository

    // The configuration is saved in the container too
    $ php app/console container:debug --parameters | grep entity_key
    sylius.config.classes        {"my_app.entity_key": {"driver":"...", "object_manager": "...", "classes":{"model":"...", "controller":"...", "repository":"...", "interface":"..."}}}

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

    Since the ``0.11`` your bundle class must implement ``ResourceBundleInterface``. You can extends the ``AbstractResourceBundle``
    which already implements this interface, it will bring you extra functionalities too.

You need to expose a semantic configuration for your bundle. The following example show you a basic ``Configuration`` that the resource bundle needs to work.

.. code-block:: php

    class Configuration implements ConfigurationInterface
    {
        public function getConfigTreeBuilder()
        {
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('bundle_name');

            $rootNode
                ->children()
                    // Driver used by the resource bundle
                    ->scalarNode('driver')->isRequired()->cannotBeEmpty()->end()

                    // Object manager used by the resource bundle, if not specified "default" will used
                    ->scalarNode('object_manager')->defaultValue('default')->end()

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
                                    // you can use an array, useful when you want to register the choice form type.
                                    ->arrayNode('form')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('default')->defaultValue('MyApp\MyCustomBundle\Form\Type\MyformType')->end()
                                            ->scalarNode('choice')->defaultValue('MyApp\MyCustomBundle\Form\Type\MyChoiceformType')->end()
                                        ->end()
                                    ->end()
                                ->end()
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

        // You can define the file formats of the files loaded
        protected $configFormat = self::CONFIG_XML;

        public function load(array $config, ContainerBuilder $container)
        {
            $this->configure(
                $config,
                new Configuration(),
                $container,
                self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS | self::CONFIGURE_FORMS
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
 * CONFIGURE_FORMS : Register the form as a service (you must register the form as array)

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
    my_app_my_entity.object_manager         default
    //...

You can overwrite the configuration of your bundle like that :

.. code-block:: php

    bundle_name:
        driver: doctrine/orm
        object_manager: my_custom_manager
        validation_groups:
            my_entity: [myCustomGroup]
        templates:
            my_entity: AppBundle:Backend/MyEntity
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
                object_manager: my_custom_manager
                classes:
                    model: %my_app.model.my_entity.class%

And your manager will be overwrite:

.. code-block:: bash

    $ php app/console container:debug | grep my_app.object_manager.other_entity_key
    my_app.object_manager.other_entity_key       n/a       alias for doctrine.odm.my_custom_manager_document_manager

And... we're done!

