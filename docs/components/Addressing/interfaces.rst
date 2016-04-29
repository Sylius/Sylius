Interfaces
==========

Model Interfaces
----------------

.. _component_addressing_model_address-interface:

AddressInterface
~~~~~~~~~~~~~~~~

This interface should be implemented by models representing the customer's address.

.. note::
   This interface extends :ref:`component_resource_model_timestampable-interface`.

   For more detailed information go to `Sylius API AddressInterface`_.

.. _Sylius API AddressInterface: http://api.sylius.org/Sylius/Component/Addressing/Model/AddressInterface.html

.. _component_addressing_model_country-interface:

CountryInterface
~~~~~~~~~~~~~~~~

This interfaces should be implemented by models representing a country.

.. note::
   This interface extends :ref:`component_resource_model_toggleable-interface`.

   For more detailed information go to `Sylius API CountryInterface`_.

.. _Sylius API CountryInterface: http://api.sylius.org/Sylius/Component/Addressing/Model/CountryInterface.html

.. _component_addressing_model_province-interface:

ProvinceInterface
~~~~~~~~~~~~~~~~~

This interface should be implemented by models representing a part of a country.

.. note::
   For more detailed information go to `Sylius API ProvinceInterface`_.

.. _Sylius API ProvinceInterface: http://api.sylius.org/Sylius/Component/Addressing/Model/ProvinceInterface.html

.. _component_addressing_model_zone-interface:

ZoneInterface
~~~~~~~~~~~~~

This interface should be implemented by models representing a single zone.

It also holds all the :doc:`/components/Addressing/zone_types`.

.. note::
   For more detailed information go to `Sylius API ZoneInterface`_.

.. _Sylius API ZoneInterface: http://api.sylius.org/Sylius/Component/Addressing/Model/ZoneInterface.html

.. _component_addressing_model_zone-member-interface:

ZoneMemberInterface
~~~~~~~~~~~~~~~~~~~

This interface should be implemented by models that represent an area a specific
zone contains, e.g. all countries in the European Union.

.. note::
   For more detailed information go to `Sylius API ZoneMemberInterface`_.

.. _Sylius API ZoneMemberInterface: http://api.sylius.org/Sylius/Component/Addressing/Model/ZoneMemberInterface.html

Service Interfaces
------------------

.. _component_addressing_checker_restricted-zone-checker-interface:

RestrictedZoneCheckerInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

A service implementing this interface should be able to check
if given :ref:`component_addressing_model_address` is in a restricted zone.

.. note::
   For more detailed information go to `Sylius API RestrictedZoneCheckerInterface`_.

.. _Sylius API RestrictedZoneCheckerInterface: http://api.sylius.org/Sylius/Component/Addressing/Checker/RestrictedZoneCheckerInterface.html

.. _component_addressing_matcher_zone-matcher-interface:

ZoneMatcherInterface
~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by a service responsible of finding the best matching zone,
and all zones containing the provided :ref:`component_addressing_model_address`.

.. note::
   For more detailed information go to `Sylius API ZoneMatcherInterface`_.

.. _Sylius API ZoneMatcherInterface: http://api.sylius.org/Sylius/Component/Addressing/Matcher/ZoneMatcherInterface.html
