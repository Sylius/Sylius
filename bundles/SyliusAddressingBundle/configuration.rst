Configuration reference
=======================

.. code-block:: yaml

    sylius_addressing:
        driver: ~ # The driver used for persistence layer.
        classes:
            address:
                model: Sylius\Bundle\AddressingBundle\Model\Address
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # You can override the repository class here.
                form: Sylius\Bundle\AddressingBundle\Form\Type\AddressType # The form type name to use.
            country:
                model: Sylius\Bundle\AddressingBundle\Model\Country
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # You can override the repository class here.
                form: Sylius\Bundle\AddressingBundle\Form\Type\CountryType # The form type class name to use.
            province:
                model: Sylius\Bundle\AddressingBundle\Model\Province
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # You can override the repository class here.
                form: Sylius\Bundle\AddressingBundle\Form\Type\ProvinceType # The form type class name to use.
            zone:
                model: Sylius\Bundle\AddressingBundle\Model\Zone
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # You can override the repository class here.
                form: Sylius\Bundle\AddressingBundle\Form\Type\ZoneType # The form type class name to use
            zone_member:
                model: Sylius\Bundle\AddressingBundle\Model\ZoneMember
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # You can override the repository class here.
                form: Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberType # The form type class name to use
            zone_member_country:
                model: Sylius\Bundle\AddressingBundle\Model\ZoneMemberCountry
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # You can override the repository class here.
                form: Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberCountryType # The form type class name to use
            zone_member_province:
                model: Sylius\Bundle\AddressingBundle\Model\ZoneMemberProvince
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # You can override the repository class here.
                form: Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberProvinceType # The form type class name to use
            zone_member_zone:
                model: Sylius\Bundle\AddressingBundle\Model\ZoneMemberZone
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # You can override the repository class here.
                form: Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberZoneType # The form type class name to use
        validation_groups:
            address: [sylius] # Address validation groups.
            country: [sylius] # Country item validation groups.
            province: [sylius] # Province item validation groups.
            zone: [sylius] # Zone item validation groups.
            zone_member: [sylius] # Zone member item validation groups.
