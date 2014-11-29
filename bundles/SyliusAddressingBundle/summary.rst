Summary
=======

Configuration Reference
-----------------------

.. code-block:: yaml

    sylius_addressing:
        # The driver used for persistence layer.
        driver: ~
        classes:
            address:
                model: Sylius\Component\Addressing\Model\Address
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\AddressingBundle\Form\Type\AddressType
            country:
                model: Sylius\Component\Addressing\Model\Country
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\AddressingBundle\Form\Type\CountryType
            province:
                model: Sylius\Component\Addressing\Model\Province
                controller: Sylius\Bundle\AddressingBundle\Controller\ProvinceController
                repository: ~
                form: Sylius\Bundle\AddressingBundle\Form\Type\ProvinceType
            zone:
                model: Sylius\Component\Addressing\Model\Zone
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\AddressingBundle\Form\Type\ZoneType
            zone_member:
                model: Sylius\Component\Addressing\Model\ZoneMember
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberType
            zone_member_country:
                model: Sylius\Component\Addressing\Model\ZoneMemberCountry
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberCountryType
            zone_member_province:
                model: Sylius\Component\Addressing\Model\ZoneMemberProvince
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberProvinceType
            zone_member_zone:
                model: Sylius\Component\Addressing\Model\ZoneMemberZone
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberZoneType
        validation_groups:
            address: [sylius]
            country: [sylius]
            province: [sylius]
            zone: [sylius]
            zone_member: [sylius]

Tests
-----

.. code-block:: bash

    $ composer install --dev --prefer-dist
    $ bin/phpspec run -fpretty --verbose

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.