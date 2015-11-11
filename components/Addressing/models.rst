Models
======

.. _component_addressing_model_address:

Address
-------

The customer's address is represented by an **Address** model. It should contain all data
concerning customer's address and as default has the following properties:

+-------------+------------------------------------+
| Property    | Description                        |
+=============+====================================+
| id          | Unique id of the address           |
+-------------+------------------------------------+
| firstName   | Customer's first name              |
+-------------+------------------------------------+
| lastName    | Customer's last name               |
+-------------+------------------------------------+
| phoneNumber | Customer's phone number            |
+-------------+------------------------------------+
| company     | Company name                       |
+-------------+------------------------------------+
| country     | Reference to a **Country** object  |
+-------------+------------------------------------+
| province    | Reference to a **Province** object |
+-------------+------------------------------------+
| street      | Address' street                    |
+-------------+------------------------------------+
| city        | Address' city                      |
+-------------+------------------------------------+
| postcode    | Address' postcode                  |
+-------------+------------------------------------+
| createdAt   | Date when address was created      |
+-------------+------------------------------------+
| updatedAt   | Date of last address' update       |
+-------------+------------------------------------+


.. note::
   This model implements the :ref:`component_addressing_model_address-interface`. |br|
   For more detailed information go to `Sylius API Address`_.

.. _Sylius API Address: http://api.sylius.org/Sylius/Component/Addressing/Model/Address.html

.. _component_addressing_model_country:

Country
-------

The geographical area of a country is represented by a **Country** model.
It should contain all data concerning a country and as default has the following properties:

+-----------+--------------------------------------+
| Property  | Description                          |
+===========+======================================+
| id        | Unique id of the country             |
+-----------+--------------------------------------+
| isoName   | Country's ISO code                   |
+-----------+--------------------------------------+
| provinces | Collection of **Province** objects   |
+-----------+--------------------------------------+
| enabled   | Indicates whether country is enabled |
+-----------+--------------------------------------+

.. note::
   This model implements the :ref:`component_addressing_model_country-interface`. |br|
   For more detailed information go to `Sylius API Country`_.

.. _Sylius API Country: http://api.sylius.org/Sylius/Component/Addressing/Model/Country.html

.. _component_addressing_model_province:

Province
--------

Smaller area inside a country is represented by a **Province** model.
You can use it to manage provinces or states and assign it to an address as well.
It should contain all data concerning a province and as default has the following properties:

+----------+----------------------------------------------+
| Property | Description                                  |
+==========+==============================================+
| id       | Unique id of the province                    |
+----------+----------------------------------------------+
| name     | Province's name                              |
+----------+----------------------------------------------+
| isoName  | Province's ISO code                          |
+----------+----------------------------------------------+
| country  | The **Country** this province is assigned to |
+----------+----------------------------------------------+

.. note::
   This model implements the :ref:`component_addressing_model_province-interface`. |br|
   For more detailed information go to `Sylius API Province`_.

.. _Sylius API Province: http://api.sylius.org/Sylius/Component/Addressing/Model/Province.html

.. _component_addressing_model_zone:

Zone
----

The geographical area is represented by a **Zone** model.
It should contain all data concerning a zone and as default has the following properties:

+----------+---------------------------------------------------------+
| Property | Description                                             |
+==========+=========================================================+
| id       | Unique id of the zone                                   |
+----------+---------------------------------------------------------+
| name     | Zone's name                                             |
+----------+---------------------------------------------------------+
| type     | Zone's type                                             |
+----------+---------------------------------------------------------+
| scope    | Zone's scope                                            |
+----------+---------------------------------------------------------+
| members  | All of the **ZoneMember** objects assigned to this zone |
+----------+---------------------------------------------------------+

.. note::
   This model implements the :ref:`component_addressing_model_zone-interface`. |br|
   For more detailed information go to `Sylius API Zone`_.

.. _Sylius API Zone: http://api.sylius.org/Sylius/Component/Addressing/Model/Zone.html

.. _component_addressing_model_zone-member:

ZoneMember
----------

In order to add a member to a zone, a class must extend abstract **ZoneMember**.
On default this model has the following properties:

+-----------+-----------------------------------------+
| Property  | Description                             |
+===========+=========================================+
| id        | Unique id of the zone member            |
+-----------+-----------------------------------------+
| belongsTo | The **Zone** this member is assigned to |
+-----------+-----------------------------------------+

.. note::
   This model implements :ref:`component_addressing_model_zone-member-interface` |br|
   For more detailed information go to `Sylius API ZoneMember`_.

.. _Sylius API ZoneMember: http://api.sylius.org/Sylius/Component/Addressing/Model/ZoneMember.html

.. note::
   Each **ZoneMember** instance holds a reference to the **Zone** object and
   an appropriate area entity, for example a **Country**.

There are three default zone member models:

* :ref:`component_addressing_model_zone-member-country`
* :ref:`component_addressing_model_zone-member-province`
* :ref:`component_addressing_model_zone-member-zone`

.. tip::
   Feel free to implement your own custom zone members!

.. _component_addressing_model_zone-member-country:

ZoneMemberCountry
-----------------

Country member of a zone is represented by a **ZoneMemberCountry** model.
It has all the properties of :ref:`component_addressing_model_zone-member` and one additional:

+----------+--------------------------------------------------+
| Property | Description                                      |
+==========+==================================================+
| country  | The **Country** associated with this zone member |
+----------+--------------------------------------------------+

.. note::
   For more detailed information go to `Sylius API ZoneMemberCountry`_.

.. _Sylius API ZoneMemberCountry: http://api.sylius.org/Sylius/Component/Addressing/Model/ZoneMemberCountry.html

.. _component_addressing_model_zone-member-province:

ZoneMemberProvince
------------------

Province member of a zone is represented by a **ZoneMemberProvince** model.
It has all the properties of :ref:`component_addressing_model_zone-member` and one additional:

+----------+---------------------------------------------------+
| Property | Description                                       |
+==========+===================================================+
| province | The **Province** associated with this zone member |
+----------+---------------------------------------------------+

.. note::
   For more detailed information go to `Sylius API ZoneMemberProvince`_.

.. _Sylius API ZoneMemberProvince: http://api.sylius.org/Sylius/Component/Addressing/Model/ZoneMemberProvince.html

.. _component_addressing_model_zone-member-zone:

ZoneMemberZone
--------------

Zone member of a zone is represented by a **ZoneMemberZone** model.
It has all the properties of :ref:`component_addressing_model_zone-member` and one additional:

+----------+-----------------------------------------------+
| Property | Description                                   |
+==========+===============================================+
| zone     | The **Zone** associated with this zone member |
+----------+-----------------------------------------------+

.. note::
   For more detailed information go to `Sylius API ZoneMemberZone`_.

.. _Sylius API ZoneMemberZone: http://api.sylius.org/Sylius/Component/Addressing/Model/ZoneMemberZone.html
