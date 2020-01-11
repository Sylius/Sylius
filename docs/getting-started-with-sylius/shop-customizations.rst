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

The first step is to detect which template is responsible for displaying the logo and therefore which should be overridden
to customize a logo image.

It's placed in **SyliusShopBundle**, at ``Resources/views/_header.html.twig.path``, so to override it,
you should create the ``templates/bundles/SyliusShopBundle/_header.html.twig`` file and copy the original file content.
Next, replace the ``img`` element source with a link to the logo or properly imported asset image (take a look at
`Symfony assets documentation <https://symfony.com/doc/current/best_practices/web-assets.html>`_ for more info).

.. hint::

    *Psst!* To speed up your learning path you can just put a logo file into the ``public/assets/`` directory. Just remember,
    it should not be committed into the repository or put on the server, it's just for the testing reasons!

At the end of customization, the overridden file would look similar to this:

.. code-block:: twig

    <div class="ui basic segment">
        <div class="ui three column stackable grid">
            <div class="column">
                <a href="{{ path('sylius_shop_homepage') }}"><img src="{{ asset('assets/logo.png') }}" alt="Logo" class="ui small image" /></a>
            </div>
            <div class="column">
                {{ sonata_block_render_event('sylius.shop.layout.header') }}
            </div>
            <div class="right aligned column">
                {{ render(url('sylius_shop_partial_cart_summary', {'template': '@SyliusShop/Cart/_widget.html.twig'})) }}
            </div>
        </div>
    </div>

A custom logo should now be displayed on the Shop panel header:

.. image:: /_images/getting-started-with-sylius/logo-after.png

Great! You've managed to customize a template in Sylius! Let's move to something a little bit more complicated but also much
more satisfying - introducing your own business logic into the system.
