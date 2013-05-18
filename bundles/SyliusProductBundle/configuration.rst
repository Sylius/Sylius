Configuration reference
=======================

.. code-block:: yaml

    sylius_product:
        driver: ~ # The driver used for persistence layer.
        engine: twig # Templating engine to use by default.
        classes:
            product:
                model: ~ # The product model class.
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\AssortmentBundle\Form\Type\ProductType
            property:
                model: Sylius\Bundle\ProductBundle\Model\Property
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\AssortmentBundle\Form\Type\PropertyType
            prototype:
                model: Sylius\Bundle\ProductBundle\Model\Prototype
                controller: Sylius\Bundle\ProductBundle\Controller\PrototypeController
                repository: ~
                form: Sylius\Bundle\AssortmentBundle\Form\Type\PrototypeType
