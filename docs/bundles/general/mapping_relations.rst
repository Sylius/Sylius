Mapping Relations
=================

All Sylius bundles use the `Doctrine RTEL functionality <http://symfony.com/doc/current/cookbook/doctrine/resolve_target_entity.html>`_, which allows to map the relations using interfaces, not implementations.

Configuration
-------------

In your ``AppKernel`` class, please register Sylius bundles before *DoctrineBundle*. This is important as we use listeners which have to be processed first.

If you do not register bundles in this order, Doctrine will throw ``Doctrine\Common\Persistence\Mapping\MappingException`` exceptions about missing classes.

Using interfaces
----------------

When defining relation to Sylius model, use the interface name instead of concrete class.
It will be automatically replaced with the configured model, enabling much greater flexibility.

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>

    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                      xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                          http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

        <entity name="Acme\ShopBundle\Entity\Product" table="sylius_product">
            <many-to-one field="taxCategory" target-entity="Sylius\Bundle\TaxationBundle\Model\TaxCategoryInterface">
               <join-column name="tax_category_id" referenced-column-name="id" nullable="true" />
            </many-to-one>
        </entity>

    </doctrine-mapping>

Thanks to this approach, if the **TaxCategory** model has changed, you do not need to worry about remapping other entities.
