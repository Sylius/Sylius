Summary
=======

Configuration Reference
-----------------------

.. code-block:: yaml

    sylius_addressing:
        # The driver used for persistence layer.
        driver: ~
        resources:
            address:
                classes:
                    model: Sylius\Component\Addressing\Model\Address
                    interface: Sylius\Component\Addressing\Model\AddressInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory: Sylius\Component\Resource\Factory\Factory
                    form: Sylius\Bundle\AddressingBundle\Form\Type\AddressType
            country:
                classes:
                    model: Sylius\Component\Addressing\Model\Country
                    interface: Sylius\Component\Addressing\Model\CountryInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory: Sylius\Component\Resource\Factory\Factory
                    form: Sylius\Bundle\AddressingBundle\Form\Type\CountryType
            province:
                classes:
                    model: Sylius\Component\Addressing\Model\Province
                    interface: Sylius\Component\Addressing\Model\ProvinceInterface
                    controller: Sylius\Bundle\AddressingBundle\Controller\ProvinceController
                    repository: ~
                    factory: Sylius\Component\Resource\Factory\Factory
                    form: Sylius\Bundle\AddressingBundle\Form\Type\ProvinceType
            zone:
                classes:
                    model: Sylius\Component\Addressing\Model\Zone
                    interface: Sylius\Component\Addressing\Model\ZoneInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory: Sylius\Component\Resource\Factory\Factory
                    form: Sylius\Bundle\AddressingBundle\Form\Type\ZoneType
            zone_member:
                classes:
                    model: Sylius\Component\Addressing\Model\ZoneMember
                    interface: Sylius\Component\Addressing\Model\ZoneMemberInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory: Sylius\Component\Resource\Factory\Factory
                    form: Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberType

Tests
-----

.. code-block:: bash

    $ composer install
    $ bin/phpspec run -fpretty --verbose

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
