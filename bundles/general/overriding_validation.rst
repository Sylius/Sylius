Overriding Validation
=====================

All Sylius validation mappings and forms are using ``sylius`` as the default group.

Changing the validation group
-----------------------------

You can configure your own validation for Sylius models. If the defaults do not fit your needs, create ``validation.xml`` inside your bundle.

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>

    <constraint-mapping xmlns="http://symfony.com/schema/dic/constraint-mapping"
                        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                        xsi:schemaLocation="http://symfony.com/schema/dic/constraint-mapping
                                            http://symfony.com/schema/dic/services/constraint-mapping-1.0.xsd">

        <class name="Sylius\Bundle\TaxationBundle\Model\TaxCategory">
            <property name="name">
                <constraint name="NotBlank">
                    <option name="message">Fill me in!</option>
                    <option name="groups">acme</option>
                </constraint>
                <constraint name="Length">
                    <option name="min">5</option>
                    <option name="max">255</option>
                    <option name="minMessage">Looonger!</option>
                    <option name="maxMessage">Shooorter!</option>
                    <option name="groups">acme</option>
                </constraint>
            </property>
        </class>

    </constraint-mapping>

You also need to configure the new validation group in ``app/config/config.yml``.

.. code-block:: yaml

    sylius_taxation:
        driver: doctrine/orm # Configure the doctrine orm driver used in documentation.
        validation_groups:
            tax_category: [acme]

Done! Now all Sylius forms will use ``acme`` validation group on all forms of tax category.
