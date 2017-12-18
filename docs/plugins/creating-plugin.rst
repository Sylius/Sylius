How to create a plugin for Sylius?
==================================

Sylius plugin is nothing more but a regular Symfony bundle adding custom behaviour to the default Sylius application.

The best way to create your own plugin is to use `Sylius plugin skeleton <https://github.com/Sylius/PluginSkeleton>`_,
which has built-in infrastructure for designing and testing using `Behat`_.

1. Create project using Composer.
---------------------------------

.. code-block:: bash

    $ composer create-project sylius/plugin-skeleton SyliusMyFirstPlugin

.. note::

    The plugin can be created anywhere, not only inside a Sylius application, because it already has the test environment inside.

2. Get familiar with basic plugin design.
-----------------------------------------

The skeleton comes with simple application that greets a customer. There are feature scenarios in ``features`` directory;
exemplary bundle with a controller, a template and a routing configuration in ``src``;
and the testing infrastructure in ``tests``.

.. note::

    The ``tests/Application`` directory contains a sample Symfony application used to test your plugin.

3. Remove boilerplate files and rename your bundle.
---------------------------------------------------

In most cases you don't want your Sylius plugin to greet the customer like it is now, so feel free to remove unnecessary
controllers, assets and features. You will also want to change the plugin's namespace from ``Acme\SyliusExamplePlugin`` to a
more meaningful one. Keep in mind that these changes also need to be done in ``tests/Application`` and ``composer.json``.

.. tip::

    Refer to chapter 5 for the naming conventions to be used.

4. Implement your awesome features.
-----------------------------------

Looking at existing Sylius plugins like

* `Sylius/ShopAPIPlugin <https://github.com/Sylius/SyliusShopApiPlugin>`_
* `bitbag-commerce/PayUPlugin <https://github.com/bitbag-commerce/PayUPlugin>`_
* `stefandoorn/sitemap-plugin <https://github.com/stefandoorn/sitemap-plugin>`_
* `bitbag-commerce/CmsPlugin <https://github.com/bitbag-commerce/CmsPlugin>`_

is a great way to start developing your own plugins.

You are strongly encouraged to use `BDD <https://www.agilealliance.org/glossary/bdd/>`_ with `Behat`_, `phpspec`_ and `PhpUnit`_
to ensure your plugin's extraordinary quality.

.. tip::

    For the plugins, the suggested way of modifying Sylius is using :doc:`the Customization Guide </customization/index>`.
    There you will find a lot of help while trying to modify templates, state machines, controllers and many, many more.

.. _`Behat`: http://behat.org/en/latest/
.. _`phpspec`: http://www.phpspec.net/en/stable/
.. _`PHPUnit`: https://phpunit.de/

5. Naming conventions
---------------------

Besides the way you are creating plugins (based on our skeleton or on your own), there are a few naming conventions that should be followed:

 * Repository name should use dashes as separator, must have a ``sylius`` prefix and a ``plugin`` suffix, e.g.: ``sylius-invoice-plugin``.
 * Bundle class name should start with vendor name, followed by ``Sylius`` and suffixed by ``Plugin`` (instead of ``Bundle``), e.g.: ``VendorNameSyliusInvoicePlugin``.
 * Bundle extension should be named similar, but suffixed by the Symfony standard ``Extension``, e.g.: ``VendorNameSyliusInvoiceExtension``.
 * Bundle class must use the ``Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait`` trait.
 * Namespace should follow _`PSR-4 <http://www.php-fig.org/psr/psr-4/>`. The top-level namespace should be the vendor name. The second-level should be prefixed by ``Sylius`` and suffixed by ``Plugin`` (e.g. ``VendorName\SyliusInvoicePlugin``)

.. note::

    Following the naming strategy for the bundle class & extension class prevents configuration key collision. Following the convention mentioned
    above generates the default configuration key as e.g. ``vendor_name_sylius_invoice_plugin``.

The rules are to be applied to all bundles which will provide an integration with the whole Sylius platform
(``sylius/sylius`` or ``sylius/core-bundle`` as dependency).

Reusable components for the whole Symfony community, which will be based just on some Sylius bundles should follow
the regular Symfony conventions.

Example
~~~~~~~

Assuming you are creating the invoicing plugin as used above, this will result in the following set-up.

**1.** Name your repository: ``vendor-name/sylius-invoice-plugin``.

**2.** Create bundle class in ``src/VendorNameSyliusInvoicePlugin.php``:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace VendorName\SyliusInvoicePlugin;

    use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
    use Symfony\Component\HttpKernel\Bundle\Bundle;

    final class VendorNameSyliusInvoicePlugin extends Bundle
    {
        use SyliusPluginTrait;
    }

**3.** Create extension class in ``src/DependencyInjection/VendorNameSyliusInvoiceExtension.php``:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace VendorName\SyliusInvoicePlugin\DependencyInjection;

    use Symfony\Component\Config\FileLocator;
    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Symfony\Component\DependencyInjection\Extension\Extension;
    use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

    final class VendorNameSyliusInvoiceExtension extends Extension
    {
        /**
         * {@inheritdoc}
         */
        public function load(array $config, ContainerBuilder $container): void
        {
            $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
            $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        }
    }

**4.** In ``composer.json``, define the correct namespacing for the PSR-4 autoloader:

.. code-block:: json

    {
        "autoload": {
            "psr-4": {
                "VendorName\\SyliusInvoicePlugin\\": "src/"
            }
        },
        "autoload-dev": {
            "psr-4": {
                "Tests\\VendorName\\SyliusInvoicePlugin\\": "tests/"
            }
        },
    }
