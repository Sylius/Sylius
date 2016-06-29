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
                    model: Sylius\Addressing\Model\Address
                    interface: Sylius\Addressing\Model\Addressinterface
                    controller: Sylius\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory: Sylius\Resource\Factory\Factory
                    form:
                        default: Sylius\AddressingBundle\Form\Type\AddressType
                validation_groups:
                    default: [ sylius ]
            country:
                classes:
                    model: Sylius\Addressing\Model\Country
                    interface: Sylius\Addressing\Model\CountryInterface
                    controller: Sylius\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory: Sylius\Resource\Factory\Factory
                    form:
                        default: Sylius\AddressingBundle\Form\Type\CountryType
                        choice: Sylius\AddressingBundle\Form\Type\CountryChoiceType
                validation_groups:
                    default: [ sylius ]
            province:
                classes:
                    model: Sylius\Addressing\Model\Province
                    interface: Sylius\Addressing\Model\ProvinceInterface
                    controller: Sylius\AddressingBundle\Controller\ProvinceController
                    repository: ~
                    factory: Sylius\Resource\Factory\Factory
                    form:
                        default: Sylius\AddressingBundle\Form\Type\ProvinceType
                        choice: Sylius\AddressingBundle\Form\Type\ProvinceChoiceType
                validation_groups:
                    default: [ sylius ]
            zone:
                classes:
                    model: Sylius\Addressing\Model\Zone
                    interface: Sylius\Addressing\Model\ZoneInterface
                    controller: Sylius\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory: Sylius\Resource\Factory\Factory
                    form:
                        default: Sylius\AddressingBundle\Form\Type\ZoneType
                        choice: Sylius\resourceBundle\Form\type\ResourceChoiceType
                validation_groups:
                    default: [ sylius ]
            zone_member:
                classes:
                    model: Sylius\Addressing\Model\ZoneMember
                    interface: Sylius\Addressing\Model\ZoneMemberInterface
                    controller: Sylius\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory: Sylius\Resource\Factory\Factory
                    form:
                        default: Sylius\AddressingBundle\Form\Type\ZoneMemberType
                validation_groups:
                    default: [ sylius ]
            zone_member_country:
                classes:
                    model: Sylius\Addressing\Model\ZoneMemberCountry
                    controller: Sylius\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory: Sylius\Resource\Factory\Factory
                    form:
                        default: Sylius\AddressingBundle\Form\Type\ZoneMemberCountryType
                validation_groups:
                    default: [ sylius ]
            zone_member_province:
                classes:
                    model: Sylius\Addressing\Model\ZoneMemberProvince
                    controller: Sylius\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory: Sylius\Resource\Factory\Factory
                    form:
                        default: Sylius\AddressingBundle\Form\Type\ZoneMemberProvinceType
                validation_groups:
                    default: [ sylius ]
            zone_member_zone:
                classes:
                    model: Sylius\Addressing\Model\ZoneMemberZone
                    controller: Sylius\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory: Sylius\Resource\Factory\Factory
                    form:
                        default: Sylius\AddressingBundle\Form\Type\ZoneMemberZoneType
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
