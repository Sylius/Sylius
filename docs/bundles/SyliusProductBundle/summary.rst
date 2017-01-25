Summary
=======

Configuration reference
-----------------------

.. code-block:: yaml

    sylius_product:
        driver: doctrine/orm
        resources:
            product:
                classes:
                    model: Sylius\Component\Core\Model\Product
                    repository: Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository
                    form: Sylius\Bundle\CoreBundle\Form\Type\Product\ProductType
                    interface: Sylius\Component\Product\Model\ProductInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    factory: Sylius\Component\Product\Factory\ProductFactory
                translation:
                    classes:
                        model: Sylius\Component\Core\Model\ProductTranslation
                        form: Sylius\Bundle\CoreBundle\Form\Type\Product\ProductTranslationType
                        interface: Sylius\Component\Product\Model\ProductTranslationInterface
                        controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                        factory: Sylius\Component\Resource\Factory\Factory
            product_variant:
                classes:
                    model: Sylius\Component\Core\Model\ProductVariant
                    repository: Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductVariantRepository
                    form: Sylius\Bundle\CoreBundle\Form\Type\Product\ProductVariantType
                    interface: Sylius\Component\Product\Model\ProductVariantInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    factory: Sylius\Component\Product\Factory\ProductVariantFactory
            product_option:
                classes:
                    repository: Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductOptionRepository
                    model: Sylius\Component\Product\Model\ProductOption
                    interface: Sylius\Component\Product\Model\ProductOptionInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    factory: Sylius\Component\Resource\Factory\TranslatableFactory
                    form: Sylius\Bundle\ProductBundle\Form\Type\ProductOptionType
                translation:
                    classes:
                        model: Sylius\Component\Product\Model\ProductOptionTranslation
                        interface: Sylius\Component\Product\Model\ProductOptionTranslationInterface
                        controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                        factory: Sylius\Component\Resource\Factory\Factory
                        form: Sylius\Bundle\ProductBundle\Form\Type\ProductOptionTranslationType
            product_option_value:
                classes:
                    model: Sylius\Component\Product\Model\ProductOptionValue
                    interface: Sylius\Component\Product\Model\ProductOptionValueInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    factory: Sylius\Component\Resource\Factory\TranslatableFactory
                    form: Sylius\Bundle\ProductBundle\Form\Type\ProductOptionValueType
                translation:
                    classes:
                        model: Sylius\Component\Product\Model\ProductOptionValueTranslation
                        interface: Sylius\Component\Product\Model\ProductOptionValueTranslationInterface
                        controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                        factory: Sylius\Component\Resource\Factory\Factory
                        form: Sylius\Bundle\ProductBundle\Form\Type\ProductOptionValueTranslationType
            product_association:
                classes:
                    model: Sylius\Component\Product\Model\ProductAssociation
                    interface: Sylius\Component\Product\Model\ProductAssociationInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    factory: Sylius\Component\Resource\Factory\Factory
                    form: Sylius\Bundle\ProductBundle\Form\Type\ProductAssociationType
            product_association_type:
                classes:
                    model: Sylius\Component\Product\Model\ProductAssociationType
                    interface: Sylius\Component\Product\Model\ProductAssociationTypeInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    factory: Sylius\Component\Resource\Factory\Factory
                    form: Sylius\Bundle\ProductBundle\Form\Type\ProductAssociationTypeType

Tests
-----

.. code-block:: bash

    $ composer install
    $ bin/phpspec run -fpretty --verbose

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
