Installation
============

We assume you're familiar with `Composer <http://packagist.org>`_, a dependency manager for PHP.

Use following command to add the bundle to your `composer.json` and download package.

.. code-block:: bash

    $ composer require sylius/addressing-bundle:*

Adding required bundles to the kernel
-------------------------------------

First, you need to enable the bundle inside the kernel.
If you're not using any other Sylius bundles, you will also need to add `SyliusResourceBundle` and its dependencies to kernel.
Don't worry, everything was automatically installed via Composer.

.. code-block:: php

    <?php

    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle($this),
            new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),
            new Sylius\Bundle\AddressingBundle\SyliusAddressingBundle(),
        );
    }

Container configuration
-----------------------

Put this configuration inside your ``app/config/config.yml``.

.. code-block:: yaml

    sylius_addressing:
        driver: doctrine/orm # Configure the doctrine orm driver used in documentation.

Routing configuration
-------------------------------

We will show an example here, how you can configure routing.
Routing is based on `SyliusResourceBundle`.

Add folowing to your ``app/config/routing.yml``.

.. code-block:: yaml

	sylius_address_list:
	    pattern: /address/list
	    defaults:
	        _controller: sylius_addressing.controller.address:getCollectionAction
	        _sylius.resource:
	            template: AcmeDemoBundle:Address:list.html.twig
	            sortable: true
	            sorting:
	                updatedAt: desc

	sylius_address_create:
	    pattern: /address/create
	    defaults:
	        _controller: sylius_addressing.controller.address:createAction
	        _sylius.resource:
	            template: AcmeDemoBundle:Address:create.html.twig
	            redirect: sylius_address_show

	sylius_address_update:
	    pattern: /address/{id}/update
	    defaults:
	        _controller: sylius_addressing.controller.address:updateAction
	        _sylius.resource:
	            template: AcmeDemoBundle:Address:update.html.twig
	            redirect: sylius_address_show

	sylius_address_delete:
	    pattern: /address/{id}/delete
	    defaults:
	        _controller: sylius_addressing.controller.address:deleteAction
	        _sylius.resource:
	            redirect: sylius_address_list

	sylius_address_show:
	    pattern: /address/{id}
	    defaults:
	        _controller: sylius_addressing.controller.address:getAction
	        _sylius.resource:
	            template: AcmeDemoBundle:Address:show.html.twig


Updating database schema
------------------------

Remember to update your database schema.

For "**doctrine/orm**" driver run the following command.

.. code-block:: bash

    $ php app/console doctrine:schema:update --force

.. warning::

    This should be done only in **dev** environment! We recommend using Doctrine migrations, to safely update your schema.

Templates
---------

We think that providing a sensible default templates is really difficult.
This is the reason why we do not currently include any, but if you have an idea for a good starter template, let us know!

The bundle requires ``list``, ``create``, ``update`` and ``show`` templates for addresses, and similar for zones.

.. note::

    You can use `the templates from our Sandbox app as inspiration <https://github.com/Sylius/Sylius-Sandbox/tree/master/src/Sylius/Bundle/SandboxBundle/Resources/views/Backend/Address>`_.
