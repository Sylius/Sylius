Introduction
============

Sylius bundles share the same architecture and conventions. This quick introduction will help you understand the usage basics.

Compatiblity
------------

All bundles are compatible with Symfony 2.3 and newer.

Naming conventions
------------------

All bundles use ``sylius`` as service/parameters prefix.

Persistence
-----------

All bundles are using Doctrine Common persistence interfaces, you should already be familiar with these concepts - ``ObjectManager`` and ``ObjectRepository``.

SyliusResourceBundle
--------------------

Every bundle which provides some models (entities), uses :doc:`SyliusResourceBundle </bundles/SyliusResourceBundle/index>`. All examples in SyliusResourceBundle documentation are valid for every model in Sylius.

For example... using a different template for the product display action is as simple as configuring the following route.

.. code-block:: yaml

    # routing.yml

    app_backend_product_show:
        pattern: /products/{id}
        methods: [GET]
        defaults:
            _controller: sylius.controller.product:showAction
            _sylius:
                template: App:Backend/Product:show.html.twig

Entities, Documents, Models
---------------------------

Sylius is using `dynamic Doctrine mapping <http://symfony.com/doc/current/cookbook/doctrine/mapping_model_classes.html>`_, so all model classes live under ``Sylius\Bundle\XyzBundle\Model``.
Appropriate metadata is loaded, based on the driver you have configured. There are no separate "Entity" or "Document" classes.

Relations mapping
-----------------

All model relations are mapped with interface names, instead of implementations. This is possible with usage of `Doctrine RTEL <http://symfony.com/doc/current/cookbook/doctrine/resolve_target_entity.html>`_.
That approach allows you to override any Model class used in Sylius, without the need to remap related models. See the example below.

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>

    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                      xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                          http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

        <mapped-superclass name="Sylius\Bundle\ProductBundle\Model\Product" table="sylius_product">
            <!-- ... -->

            <one-to-many field="properties" target-entity="Sylius\Bundle\ProductBundle\Model\ProductPropertyInterface" mapped-by="product">
                <cascade>
                    <cascade-all />
                </cascade>
            </one-to-many>
        </mapped-superclass>

    </doctrine-mapping>

If you change the class of ProductProperty model, this relation will be automatically updated.

Managers, Repositories and Controllers
--------------------------------------

Every model in Sylius bundles has its own manager, repository and controller services.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $productRepository = $this->get('sylius.repository.product');
        $taxRateRepository = $this->get('sylius.repository.tax_rate')

        $productManager = $this->get('sylius.manager.product'); // Actually, these are just aliases to EntityManager/DocumentManager.
        $taxRateManager = $this->get('sylius.manager.tax_rate'); 
    }

.. code-block:: yaml

    # routing.yml

    sylius_product_show:
        pattern: /{id}
        methods: [GET]
        defaults:
            _controller: sylius.controller.product:showAction

    sylius_tax_rate_delete:
        pattern: /{id}
        methods: [DELETE]
        defaults:
            _controller: sylius.controller.tax_rate:deleteAction

Overriding Classes
------------------

Every class of a particular model, repository, controller or form type can be overriden directly via its bundle configuration.

.. code-block:: yaml

    sylius_product:
        driver: doctrine/orm
        classes:
            product:
                model: App\ShopBundle\Entity\Product
                controller: App\ShopBundle\Controller\ProductController
                repository: App\ShopBundle\Repository\ProductRepository
                form: App\ShopBundle\Form\Type\ProductType

Validation Mappings
-------------------

All default forms and mappings use ``sylius`` as validation group. If you want to use different validation rules, you can configure it for every model.

.. code-block:: yaml

    sylius_product:
        driver: doctrine/orm
        classes:
            product:
                model: App\ShopBundle\Entity\Product
        validation_groups:
            product:
                - acme
            property:
                - emca

Default Models
--------------

All bundles ship with already mapped entities. If you configure your own custom classes, the default models automagically become mapped super classes.
