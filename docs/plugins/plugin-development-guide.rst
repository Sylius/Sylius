Plugin Development Guide
========================

Sylius plugins are one of the most powerful ways to extend Sylius functionality. They're not bounded by Sylius release cycle and can be
developed quicker and more effectively. They also allow sharing our (developers) work in an open-source community, which is not possible with
regular application customizations.

BDD methodology says the most accurate way to explain some process is using an example.
With respect to that rule, let's create some simple first plugin together!


Idea
----

The most important thing is a concept. You should be aware, that not every customization should be made as a plugin for Sylius.
If you:

* share the common logic between multiple projects
* think provided feature could be useful for the whole Sylius community and want to share it for free or sell it

then you should definitely consider the creation of a plugin. On the other hand, if:

* your feature is specific for your project
* you don't want to share your work in the community (maybe **yet**)

then don't be afraid to make a regular Sylius customization.

For needs of this tutorial, we would implement a simple plugin, making possible to mark product variant **available on demand**.


How to start?
-------------

The first step is to create a new plugin using our ``PluginSkeleton``.

.. code-block:: bash

    $ composer create-project sylius/plugin-skeleton IronManSyliusProductOnDemandPlugin

.. note::

    Remember about naming convention! Sylius plugin should start with your vendor name, followed by ``Sylius`` prefix and with ``Plugin`` suffix at the end.
    Let's say your vendor name is **IronMan**

Naming changes
**************

``PluginSkeleton`` provides some default classes and configurations. However, they must have some default values and names that should be changed,
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

* In ``tests/Application/app/AppKernel.php``:

    * ``\Acme\SyliusExamplePlugin\AcmeSyliusExamplePlugin()`` -> ``\IronMan\SyliusProductOnDemandPlugin\SyliusProductOnDemandPlugin()``

That's it! All other files are just a boilerplate to show you what can be done in the Sylius plugin. They can be deleted with no harm.

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

    You **don't** have to remove all these files mentioned above. They can be adapted to suit your plugin functionality. However, as
    they provide a default, dummy features only for the presentation reasons, it's just easier to delete them and implement new ones by
    your own.

Specifications
--------------

We strongly encourage you to follow our BDD path in implementing Sylius plugins. In fact, proper tests are one of the requirements to
(:doc:`have your plugin officially accepted</plugins/index>`).

.. attention::

    Even though we're big fans of our Behat and PHPSpec-based workflow, we do not enforce you to use the same libraries.
    We strongly believe that properly tested code is the biggest value, but everyone should feel well with their own tests.
    If you're not familiar with PHPSpec, but know PHPUnit (or anything else) by heart - keep rocking with your favorite tool!

Scenario
********

Let's start with describing how **marking a product variant available on demand** should work::

    @managing_product_variants
    Feature: Marking a variant as available on demand
        In order to inform customer about possibility to order a product variant on demand
        As an Administrator
        I want to be able to mark product variant as available on demand

        Background:
            Given the store has a "Iron Man Suite" configurable product
            And this product has "Mark XLVI" variant
            And I am logged in as an administrator

        @ui
        Scenario: Marking product as available on demand
            When I want to modify the "Mark XLVI" product variant
            And I mark it as available on demand
            And I save my changes
            Then I should be notified that it has been successfully edited
            And inventory of this variant should be available on demand

What is really important, usually you don't need to write the whole Behat scenario on your own! In the example above only 2 steps
would need a custom implementation. Rest of them can be easily reused from **Sylius** Behat system.
