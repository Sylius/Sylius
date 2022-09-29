Shop Customizations
===================

What makes Sylius unique from other e-commerce systems is not only its highly developed community or clean code base. The developer
experience has always been a great advantage of this platform - and it includes easiness of customization and great extendability.

Let's get the benefit from these features and make some simple customization, to make your store even more suitable for your
business needs.

Logo
----

You can start with the shop panel. The default templates are elegant and straightforward, but for sure you would like
to make them unique for your online store. Maybe some colors should be different? Or even the whole product page does
not look like you want? Fortunately, twig templates are easy to override or customize (take a look at
:doc:`Customizing Templates chapter</customization/template>` for more info).

In the beginning, try a very simple, but also one of the most crucial changes - displaying your shop logo in place of the Sylius logo.

Default logo in shop panel:

.. image:: /_images/getting-started-with-sylius/logo-before.png

Firstly, we need to add our logo to the project. You can do it by copying your logo to the ``<project_root>/assets/shop/images/logo.png``
and importing it in ``<project_root>/assets/shop/entry.js``.

Your ``entry.js`` should look like this:

.. code-block:: javascript

    import './images/logo.png';

Now you should run ``yarn build`` to rebuild the assets.

The second step is to detect which template is responsible for displaying the logo and therefore which should be overridden
to customize a logo image.

It's placed in **SyliusShopBundle**, at ``Resources/views/Layout/Header/_logo.html.twig`` path, so to override it,
you should create the ``templates/bundles/SyliusShopBundle/Layout/Header/_logo.html.twig`` file and copy the original file content.
Next, replace the ``img`` element source with a link to the logo or properly imported asset image (take a look at
:doc:`/book/frontend/managing-assets` for more info).

The other way to achieve this is to modify the configuration of the ``sylius.shop.layout.header.grid`` template event.
Here for sake of example the same logo file ``templates/shop/Layout/Header/_logo.html.twig`` used as in the example above.
Add the configuration to the file that stores your sylius template event settings:

.. code-block:: yaml

    # config.yaml

    sylius_ui:
        events:
            sylius.shop.layout.header.grid:
                blocks:
                    logo: 'shop/Layout/Header/_logo.html.twig'

If you want to learn more about template customization with sylius template events - click :doc:`here</customization/template>`.

.. hint::

    We encourage to create and register another ``.yaml`` file to store template changes for more clarity in configuration files.

At the end of customization, the overridden file would look similar to this:

.. code-block:: twig

    <div class="column">
        <a href="{{ path('sylius_shop_homepage') }}"><img src="{{ asset('build/app/shop/images/logo.png', 'app.shop') }}" alt="Logo" class="ui small image" /></a>
    </div>

A custom logo should now be displayed on the Shop panel header:

.. image:: /_images/getting-started-with-sylius/logo-after.png

Great! You've managed to customize a template in Sylius! Let's move to something a little bit more complicated but also much
more satisfying - introducing your own business logic into the system.
