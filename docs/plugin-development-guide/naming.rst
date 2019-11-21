Naming changes
--------------

``PluginSkeleton`` provides some default classes and configurations. However, they must have some default values and names that should be changed
to reflect your plugin functionality. Basing on the vendor and plugin names established above, these are the changes that should be made:

* In ``composer.json``:

    * ``sylius/plugin-skeleton`` -> ``iron-man/sylius-product-on-demand-plugin``

    * ``Acme example plugin for Sylius.`` -> ``Plugin allowing to mark product variants as available on demand in Sylius.`` (or sth similar)

    * ``Acme\\SyliusExamplePlugin\\`` -> ``IronMan\\SyliusProductOnDemandPlugin\\`` (the same changes should be done in namespaces in ``src/`` directory

    * ``Tests\\Acme\\SyliusExamplePlugin\\`` -> ``Tests\\IronMan\\SyliusProductOnDemandPlugin\\`` (the same changes should be done in namespaces in ``tests/`` directory

* ``AcmeSyliusExamplePlugin`` should be renamed to ``IronManSyliusProductOnDemandPlugin``

* ``AcmeSyliusExampleExtension`` should be renamed to ``IronManSyliusProductOnDemandExtension``

* In ``src/DependencyInjection/Configuration.php``:

    * ``acme_sylius_example_plugin`` -> ``iron_man_sylius_product_on_demand_plugin``

* In ``tests/Application/Kernel.php``:

    * ``\Acme\SyliusExamplePlugin\AcmeSyliusExamplePlugin()`` -> ``\IronMan\SyliusProductOnDemandPlugin\SyliusProductOnDemandPlugin()``

* In ``phpspec.yml.dist`` (if you want to use PHPSpec in your plugin):

    * ``Acme\SyliusExamplePlugin`` -> ``IronMan\SyliusProductOnDemandPlugin``

That's it! All other files are just a boilerplate to show you what can be done in the Sylius plugin. They can be deleted with no harm:

* All files from ``features/`` directory

* ``src/Controller/GreetingController.php``

* ``src/Resources/config/admin_routing.yml``

* ``src/Resources/config/shop_routing.yml``

* ``src/Resources/public/greeting.js``

* ``src/Resources/views/dynamic_greeting.html.twig``

* ``src/Resources/views/static_greeting.html.twig``

* All files from ``tests/Behat/Page/Shop/`` (with corresponding services)

* ``tests/Context/Ui/Shop/WelcomeContext.php`` (with corresponding service)

You should also delete Behat suite named ``greeting_customer`` from ``tests/Behat/Resources/suites.yml``.

.. important::

    You **don't have to** remove all these files mentioned above. They can be adapted to suit your plugin functionality. However, as
    they provide default, dummy features only for the presentation reasons, it's just easier to delete them and implement new ones on
    your own.
