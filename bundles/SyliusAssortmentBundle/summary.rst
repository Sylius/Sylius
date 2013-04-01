Summary
=======

Configuration reference
-----------------------

.. code-block:: yaml

    sylius_assortment:
        driver: ~ # The driver used for persistence layer.
        engine: twig # Templating engine to use by default.
        classes:
            product:
                model: ~ # The product model class.
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # Product repository class.
                form: Sylius\Bundle\AssortmentBundle\Form\Type\ProductType # Product form type class name.
            variant:
                model: ~ # The variant model class.
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # Variant repository class.
                form: Sylius\Bundle\AssortmentBundle\Form\Type\VariantType # Variant form type class name.
            option:
                model: ~ # The option model class.
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # Option repository class.
                form: Sylius\Bundle\AssortmentBundle\Form\Type\OptionType # Option form type class name.
            property:
                model: ~ # The property model class.
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # Property repository class.
                form: Sylius\Bundle\AssortmentBundle\Form\Type\PropertyType # Property form type class name.
            prototype:
                model: ~ # The prototype model class.
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # Prototype repository class.
                form: Sylius\Bundle\AssortmentBundle\Form\Type\PrototypeType # Prototype form type class name.

`phpspec2 <http://phpspec.net>`_ examples
-----------------------------------------

.. code-block:: bash

    $ composer install --dev --prefer-dist
    $ bin/phpspec run -f pretty

Working examples
----------------

If you want to see working implementation, try out the `Sylius application <http://github.com/Sylius/Sylius>`_.

There is also an example that shows how to integrate this bundle into `Symfony Standard Edition <https://github.com/umpirsky/symfony-standard/tree/sylius/assortment-bundle>`_.

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/SyliusAssortmentBundle/issues>`_.
If you have found bug, please create an issue.
