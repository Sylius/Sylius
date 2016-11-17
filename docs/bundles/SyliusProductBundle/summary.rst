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
                    form:
                        default: Sylius\Bundle\CoreBundle\Form\Type\Product\ProductType
                        variant_generation: Sylius\Bundle\ProductBundle\Form\Type\ProductVariantGenerationType
                        choice: Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType
                        from_identifier: Sylius\Bundle\ResourceBundle\Form\Type\ResourceFromIdentifierType
                    interface: Sylius\Component\Product\Model\ProductInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    factory: Sylius\Component\Product\Factory\ProductFactory
                translation:
                    classes:
                        model: Sylius\Component\Core\Model\ProductTranslation
                        form:
                            default: Sylius\Bundle\CoreBundle\Form\Type\Product\ProductTranslationType
                        interface: Sylius\Component\Product\Model\ProductTranslationInterface
                        controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                        factory: Sylius\Component\Resource\Factory\Factory
                    validation_groups:
                        default: [ sylius ]
                validation_groups:
                    default: [ sylius ]
                    variant_generation: [ sylius ]
            product_variant:
                classes:
                    model: Sylius\Component\Core\Model\ProductVariant
                    repository: Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductVariantRepository
                    form:
                        default: Sylius\Bundle\CoreBundle\Form\Type\Product\ProductVariantType
                        from_identifier: Sylius\Bundle\ResourceBundle\Form\Type\ResourceFromIdentifierType
                    interface: Sylius\Component\Product\Model\ProductVariantInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    factory: Sylius\Component\Product\Factory\ProductVariantFactory
                validation_groups:
                    default: [ sylius ]
                    from_identifier: [ sylius ]
            product_option:
                classes:
                    repository: Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductOptionRepository
                    model: Sylius\Component\Product\Model\ProductOption
                    interface: Sylius\Component\Product\Model\ProductOptionInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    factory: Sylius\Component\Resource\Factory\TranslatableFactory
                    form:
                        default: Sylius\Bundle\ProductBundle\Form\Type\ProductOptionType
                        choice: Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType
                validation_groups:
                    default: [ sylius ]
                translation:
                    classes:
                        model: Sylius\Component\Product\Model\ProductOptionTranslation
                        interface: Sylius\Component\Product\Model\ProductOptionTranslationInterface
                        controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                        factory: Sylius\Component\Resource\Factory\Factory
                        form:
                            default: Sylius\Bundle\ProductBundle\Form\Type\ProductOptionTranslationType
                    validation_groups:
                        default: [ sylius ]
            product_option_value:
                classes:
                    model: Sylius\Component\Product\Model\ProductOptionValue
                    interface: Sylius\Component\Product\Model\ProductOptionValueInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    factory: Sylius\Component\Resource\Factory\TranslatableFactory
                    form:
                        default: Sylius\Bundle\ProductBundle\Form\Type\ProductOptionValueType
                validation_groups:
                    default: [ sylius ]
                translation:
                    classes:
                        model: Sylius\Component\Product\Model\ProductOptionValueTranslation
                        interface: Sylius\Component\Product\Model\ProductOptionValueTranslationInterface
                        controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                        factory: Sylius\Component\Resource\Factory\Factory
                        form:
                            default: Sylius\Bundle\ProductBundle\Form\Type\ProductOptionValueTranslationType
                    validation_groups:
                        default: [ sylius ]
            product_association:
                classes:
                    model: Sylius\Component\Product\Model\ProductAssociation
                    interface: Sylius\Component\Product\Model\ProductAssociationInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    factory: Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\ProductBundle\Form\Type\ProductAssociationType
                validation_groups:
                    default: [ sylius ]
            product_association_type:
                classes:
                    model: Sylius\Component\Product\Model\ProductAssociationType
                    interface: Sylius\Component\Product\Model\ProductAssociationTypeInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    factory: Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\ProductBundle\Form\Type\ProductAssociationTypeType
                        choice: Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType
                validation_groups:
                    default: [ sylius ]

Tests
-----

.. code-block:: bash

    $ composer install
    $ bin/phpspec run -fpretty --verbose

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
