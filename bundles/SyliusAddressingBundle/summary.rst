Summary
=======

Configuration reference
-----------------------

.. code-block:: yaml

    sylius_addressing:
        driver: ~ # The driver used for persistence layer.
        engine: twig # Templating engine to use by default.
        classes:
            address:
                model: ~ # The address model class.
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # You can override the repository class here.
                form: Sylius\Bundle\AddressingBundle\Form\Type\AddressType # The form type name to use.
            country:
                model: ~ # The country model class.
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # You can override the repository class here.
                form: Sylius\Bundle\AddressingBundle\Form\Type\CountryType # The form type class name to use.
            province:
                model: ~ # The province model class.
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # You can override the repository class here.
                form: Sylius\Bundle\AddressingBundle\Form\Type\ProvinceType # The form type class name to use.
            zone:
                model: ~ # The zone model class.
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # You can override the repository class here.
                form: Sylius\Bundle\AddressingBundle\Form\Type\ZoneType # The form type class name to use
            zone_member:
                model: ~ # The zone member model class.
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # You can override the repository class here.
                form: Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberType # The form type class name to use
            zone_member_country:
                model: ~ # The country zone member model class.
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # You can override the repository class here.
                form: Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberCountryType # The form type class name to use
            zone_member_province:
                model: ~ # The province zone member model class.
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # You can override the repository class here.
                form: Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberProvinceType # The form type class name to use
            zone_member_zone:
                model: ~ # The zone zone member model class.
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # You can override the repository class here.
                form: Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberZoneType # The form type class name to use

`phpspec2 <http://phpspec.net>`_ examples
-----------------------------------------

.. code-block:: bash

    $ composer install --dev --prefer-dist
    $ bin/phpspec run -f pretty

Working examples
----------------

If you want to see working implementation, try out the `Sylius sandbox application <http://github.com/Sylius/Sylius-Sandbox>`_.

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/SyliusAddressingBundle/issues>`_.
If you have found bug, please create an issue.
