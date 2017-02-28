Custom Action
=============

There are certain cases when built-in action types are not enough.

All you need to do is create your own action template and register it for the ``sylius_grid``.

In the template we will specify the button's icon to be ``mail`` and its colour to be ``purple``.

.. code-block:: twig

    {% import '@SyliusUi/Macro/buttons.html.twig' as buttons %}

    {% set path = options.link.url|default(path(options.link.route)) %}

    {{ buttons.default(path, action.label, null, 'mail', 'purple') }}

Now configure the new action's template like below in the ``app/config/config.yml``:

.. code-block:: yaml

    # app/config/config.yml
    sylius_grid:
        templates:
            action:
                contactSupplier: "@App/Grid/Action/contactSupplier.html.twig"

From now on you can use your new action type in the grid configuration!

Let's assume that you already have a route for contacting your suppliers, then you can configure the grid action:

.. code-block:: yaml

    sylius_grid:
        grids:
            app_admin_supplier:
                driver:
                    name: doctrine/orm
                    options:
                        class: AppBundle\Entity\Supplier
                actions:
                    item:
                        contactSupplier:
                            type: contactSupplier
                            label: Contact Supplier
                            options:
                                link:
                                    route: app_admin_contact_supplier
                                    parameters:
                                        id: resource.id
