Summary
=======

Configuration reference
-----------------------

.. code-block:: yaml

    sylius_product:
        driver: ~ # The driver used for persistence layer.
        engine: twig # Templating engine to use by default.
        classes:
            product:
                model: Sylius\Component\Product\Model\Product
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\AssortmentBundle\Form\Type\ProductType
            product_prototype:
                model: Sylius\Component\Product\Model\Prototype
                controller: Sylius\Bundle\ProductBundle\Controller\PrototypeController
                repository: ~
                form: Sylius\Bundle\AssortmentBundle\Form\Type\PrototypeType
        validation_groups:
            product: [sylius] # Product validation groups.
            product_prototype: [sylius] # Product prototype validation groups.
Tests
-----

.. code-block:: bash

    $ composer install --dev --prefer-dist
    $ bin/phpspec run -fpretty --verbose

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.