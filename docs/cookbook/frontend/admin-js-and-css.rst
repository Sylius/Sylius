How to customize Admin JS & CSS?
================================

It is sometimes required to add your own JSS and CSS files for Sylius Admin. Achieving that is really straightforward.

We will now teach you how to do it!

How to add custom JS to Admin?
------------------------------

**1. Prepare your own JS file:**

As an example we will use a popup window script, it is easy for manual testing.

.. code-block:: javascript

    // web/assets/admin/js/custom.js
    window.confirm("Your custom JS was loaded correctly!");

**2. Prepare a file with your JS include, you can use the include template from SyliusUiBundle:**

.. code-block:: twig

    {# src/AppBundle/Resources/views/Admin/_javascripts.html.twig #}
    {% include 'SyliusUiBundle::_javascripts.html.twig' with {'path': 'assets/admin/js/custom.js'} %}

**3. Use the Sonata block event to insert your javascripts:**

.. tip::

    Learn more about customizing templates via events in the customization guide :doc:`here </customization/template>`.

.. code-block:: yaml

    # src/AppBundle/Resources/config/services.yml
    services:
        app.block_event_listener.admin.layout.javascripts:
            class: Sylius\Bundle\UiBundle\Block\BlockEventListener
            arguments:
                - '@@App/Admin/_javascripts.html.twig'
            tags:
                - { name: kernel.event_listener, event: sonata.block.event.sylius.admin.layout.javascripts, method: onBlockEvent }

**4. Additionally, to make sure everything is loaded run gulp:**

.. code-block:: bash

    $ yarn build

**5. Go to Sylius Admin and check the results!**

How to add custom CSS to Admin?
-------------------------------

**1. Prepare your own CSS file:**

As an example we will change the sidebar menu background color, what is clearly visible at first sight.

.. code-block:: css

    // web/assets/admin/css/custom.css
    #sidebar {
        background-color: #1abb9c;
    }

**2. Prepare a file with your CSS include, you can use the include template from SyliusUiBundle:**

.. code-block:: twig

    {# src/AppBundle/Resources/views/Admin/_stylesheets.html.twig #}
    {% include 'SyliusUiBundle::_stylesheets.html.twig' with {'path': 'assets/admin/css/custom.css'} %}

**3. Use the Sonata block event to insert your stylesheets:**

.. tip::

    Learn more about customizing templates via events in the customization guide :doc:`here </customization/template>`.

.. code-block:: yaml

    # src/AppBundle/Resources/config/services.yml
    services:
        app.block_event_listener.admin.layout.stylesheets:
            class: Sylius\Bundle\UiBundle\Block\BlockEventListener
            arguments:
                - '@@App/Admin/_stylesheets.html.twig'
            tags:
                - { name: kernel.event_listener, event: sonata.block.event.sylius.admin.layout.stylesheets, method: onBlockEvent }

**4. Additionally, to make sure everything is loaded run gulp:**

.. code-block:: bash

    $ yarn build

**5. Go to Sylius Admin and check the results!**

Learn more
----------

* :doc:`Templates customizing </customization/template>`
