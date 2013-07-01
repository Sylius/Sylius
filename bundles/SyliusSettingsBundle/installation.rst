Installation
============

We assume you're familiar with `Composer <http://packagist.org>`_, a dependency manager for PHP.

Use following command to add the bundle to your `composer.json` and download package.

.. code-block:: bash

    $ composer require sylius/settings-bundle:*

Adding required bundles to the kernel
-------------------------------------

First, you need to enable the bundle inside the kernel.
If you're not using any other Sylius bundles, you will also need to add `SyliusResourceBundle` and its dependencies to kernel.
This bundle also uses `LiipDoctrineCacheBundle`. Don't worry, everything was automatically installed via Composer.

.. code-block:: php

    <?php

    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            new Liip\DoctrineCacheBundle\LiipDoctrineCacheBundle(),
            new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),
            new Sylius\Bundle\SettingsBundle\SyliusSettingsBundle(),

            // Other bundles...
        );
    }

Creating your settings schema
-----------------------------

You have to implement **SchemaInterface** in order to be able to save setting.
Lets define for example our page metadata like this:

.. code-block:: php

    <?php

    // src/Acme/DemoBundle/Settings/MetaSettingsSchema.php
    namespace Acme\DemoBundle\Settings;

    use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
    use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
    use Symfony\Component\Form\FormBuilderInterface;

    class MetaSettingsSchema implements SchemaInterface
    {
        public function buildSettings(SettingsBuilderInterface $builder)
        {
            $builder
                ->setDefaults(array(
                    'title'            => 'Sylius - Modern ecommerce for Symfony2',
                    'meta_keywords'    => 'symfony, sylius, ecommerce, webshop, shopping cart',
                    'meta_description' => 'Sylius is modern ecommerce solution for PHP. Based on the Symfony2 framework.',
                ))
                ->setAllowedTypes(array(
                    'title'            => array('string'),
                    'meta_keywords'    => array('string'),
                    'meta_description' => array('string'),
                ))
            ;
        }

        public function buildForm(FormBuilderInterface $builder)
        {
            $builder
                ->add('title')
                ->add('meta_keywords')
                ->add('meta_description', 'textarea')
            ;
        }
    }

As you can see there are two methods in our schema, and both of them are very simple. First one ``->buildSettings()``
defines default values and allowed data types. ``->buildForm()`` creates form to be used in web interface to update settings.

Container configuration
-----------------------

Put this configuration inside your ``app/config/config.yml``.

.. code-block:: yaml

    sylius_settings:
        driver: doctrine/orm

    liip_doctrine_cache:
        namespaces:
            sylius_settings:
                type: file_system

Now, lets register our **MetaSettingsSchema** service. Note that we are tagging it as `sylius.settings_schema`:

.. code-block:: xml

    <service id="acme.demo.settings_schema.meta" class="Acme\DemoBundle\Settings\MetaSettingsSchema">
        <tag name="sylius.settings_schema" namespace="default" />
    </service>

Importing routing configuration
-------------------------------

Import default routing from your ``app/config/routing.yml``.

.. code-block:: yaml

    sylius_settings_meta:
        resource: @SyliusSettingsBundle/Resources/config/routing.yml
        prefix: /meta

.. note::

    We used ``default`` namespace in this example. If you want to use other namespaces for saving your settings, routing config should
    be updated as it contains namespace parameter.

Updating database schema
------------------------

Remember to update your database schema.

For "**doctrine/orm**" driver run the following command.

.. code-block:: bash

    $ php app/console doctrine:schema:update --force

.. warning::

    This should be done only in **dev** environment! We recommend using Doctrine migrations, to safely update your schema.
