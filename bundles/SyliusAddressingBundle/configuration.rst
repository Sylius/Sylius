Configuration Reference
=======================

.. code-block:: yaml

    sylius_addressing:
        driver: ~ # The driver used for persistence layer.
        classes:
            address:
                model: Sylius\Bundle\AddressingBundle\Model\Address
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\AddressingBundle\Form\Type\AddressType
            country:
                model: Sylius\Bundle\AddressingBundle\Model\Country
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\AddressingBundle\Form\Type\CountryType
            province:
                model: Sylius\Bundle\AddressingBundle\Model\Province
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\AddressingBundle\Form\Type\ProvinceType
            zone:
                model: Sylius\Bundle\AddressingBundle\Model\Zone
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\AddressingBundle\Form\Type\ZoneType
            zone_member:
                model: Sylius\Bundle\AddressingBundle\Model\ZoneMember
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberType
            zone_member_country:
                model: Sylius\Bundle\AddressingBundle\Model\ZoneMemberCountry
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberCountryType
            zone_member_province:
                model: Sylius\Bundle\AddressingBundle\Model\ZoneMemberProvince
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberProvinceType
            zone_member_zone:
                model: Sylius\Bundle\AddressingBundle\Model\ZoneMemberZone
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberZoneType
        validation_groups:
            address: [sylius]
            country: [sylius]
            province: [sylius]
            zone: [sylius]
            zone_member: [sylius]
