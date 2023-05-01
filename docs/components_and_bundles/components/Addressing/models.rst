.. rst-class:: outdated

Models
======

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

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
| country     | Country's ISO code                 |
+-------------+------------------------------------+
| province    | Province's code                    |
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
   This model implements the :ref:`component_addressing_model_address-interface`.

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
| code      | Country's ISO code                   |
+-----------+--------------------------------------+
| provinces | Collection of **Province** objects   |
+-----------+--------------------------------------+
| enabled   | Indicates whether country is enabled |
+-----------+--------------------------------------+

.. note::
   This model implements the :ref:`component_addressing_model_country-interface`
   and `CodeAwareInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/CodeAwareInterface.php>`_.

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
| code     | Unique code of the province                  |
+----------+----------------------------------------------+
| name     | Province's name                              |
+----------+----------------------------------------------+
| country  | The **Country** this province is assigned to |
+----------+----------------------------------------------+

.. note::
   This model implements the :ref:`component_addressing_model_province-interface`
   and `CodeAwareInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/CodeAwareInterface.php>`_.

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
| code     | Unique code of the zone                                 |
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
   This model implements the :ref:`component_addressing_model_zone-interface`
   and `CodeAwareInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/CodeAwareInterface.php>`_.

.. _component_addressing_model_zone-member:

ZoneMember
----------

In order to add a specific location to a **Zone**,
an instance of **ZoneMember** must be created with that location's code.
On default this model has the following properties:

+-----------+------------------------------------------------------+
| Property  | Description                                          |
+===========+======================================================+
| id        | Unique id of the zone member                         |
+-----------+------------------------------------------------------+
| code      | Unique code of affiliated member i.e. country's code |
+-----------+------------------------------------------------------+
| belongsTo | The **Zone** this member is assigned to              |
+-----------+------------------------------------------------------+

.. note::
   This model implements :ref:`component_addressing_model_zone-member-interface`
   and `CodeAwareInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/CodeAwareInterface.php>`_.
