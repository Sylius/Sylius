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
                    interface: Sylius\Component\Addressing\Model\Addressinterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory: Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\AddressingBundle\Form\Type\AddressType
                validation_groups:
                    default: [ sylius ]
            country:
                classes:
                    model: Sylius\Component\Addressing\Model\Country
                    interface: Sylius\Component\Addressing\Model\CountryInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory: Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\AddressingBundle\Form\Type\CountryType
                        choice: Sylius\Bundle\AddressingBundle\Form\Type\CountryChoiceType
                validation_groups:
                    default: [ sylius ]
            province:
                classes:
                    model: Sylius\Component\Addressing\Model\Province
                    interface: Sylius\Component\Addressing\Model\ProvinceInterface
                    controller: Sylius\Bundle\AddressingBundle\Controller\ProvinceController
                    repository: ~
                    factory: Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\AddressingBundle\Form\Type\ProvinceType
                        choice: Sylius\Bundle\AddressingBundle\Form\Type\ProvinceChoiceType
                validation_groups:
                    default: [ sylius ]
            zone:
                classes:
                    model: Sylius\Component\Addressing\Model\Zone
                    interface: Sylius\Component\Addressing\Model\ZoneInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory: Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\AddressingBundle\Form\Type\ZoneType
                        choice: Sylius\Bundle\resourceBundle\Form\type\ResourceChoiceType
                validation_groups:
                    default: [ sylius ]
            zone_member:
                classes:
                    model: Sylius\Component\Addressing\Model\ZoneMember
                    interface: Sylius\Component\Addressing\Model\ZoneMemberInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory: Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberType
                validation_groups:
                    default: [ sylius ]
            zone_member_country:
                classes:
                    model: Sylius\Component\Addressing\Model\ZoneMemberCountry
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory: Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberCountryType
                validation_groups:
                    default: [ sylius ]
            zone_member_province:
                classes:
                    model: Sylius\Component\Addressing\Model\ZoneMemberProvince
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory: Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberProvinceType
                validation_groups:
                    default: [ sylius ]
            zone_member_zone:
                classes:
                    model: Sylius\Component\Addressing\Model\ZoneMemberZone
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory: Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberZoneType
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
