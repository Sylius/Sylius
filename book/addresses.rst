.. index::
   single: Addresses

Addresses
=========

Every address in Sylius is represented by *Address* model. Default structure has the following fields:

* firstname
* lastname
* street
* city
* postcode
* reference to *Country*
* reference to *Province* (optional)
* createdAt
* updatedAt

Countries
---------

Every country to which you will be shipping your goods lives as *Country* entity. Country consists of `name` and `isoName`.

Provinces
---------

*Province* is a smaller area inside of a *Country*. You can use it to manage provinces or states and assign it to an address as well.

+-----------+--------------------------------+
| Attribute | Description                    |
+===========+================================+
| id        | Unique id of the province      |
+-----------+--------------------------------+
| name      |                                |
+-----------+--------------------------------+
| country   | Reference to a country         |
+-----------+--------------------------------+
| createdAt | Date when province was created |
+-----------+--------------------------------+
| updatedAt | Date of last update            |
+-----------+--------------------------------+

Zones
-----

This library allows you to define **Zones**, which represent a specific geographical area.

Zone Model
~~~~~~~~~~

Every **Zone** is represented by the following model:

+-----------+--------------------------------+
| Attribute | Description                    |
+===========+================================+
| id        | Unique id of the zone          |
+-----------+--------------------------------+
| name      |                                |
+-----------+--------------------------------+
| type      | String type of zone            |
+-----------+--------------------------------+
| members   | Zone members                   |
+-----------+--------------------------------+
| createdAt | Date when zone was created     |
+-----------+--------------------------------+
| updatedAt | Date of last update            |
+-----------+--------------------------------+

Three different types of zones are supported out-of-the-box.

* `country` zone, which consists of many countries.
* `province` zone, which is constructed from multiple provinces.
* `zone`, which is a group of other zones.

Each zone type has different **ZoneMember** model, but they all expose the same API:

There are following models and each of them represents a different zone member:

* ZoneMemberCountry
* ZoneMemberProvince
* ZoneMemberZone

Matching a Zone
---------------

Zones are not very useful by themselves, but they can be part of a complex taxation/shipping or any other system.
A service implementing the `ZoneMatcherInterface` is responsible for matching the **Address** to a specific **Zone**.

.. code-block:: php

    <?php

    $zoneMatcher = $this->get('sylius.zone_matcher');
    $zone = $zoneMatcher->match($user->getAddress());

Zone matcher can also return all matching zones. (not only the best one)

.. code-block:: php

    <?php

    $zoneMatcher = $this->get('sylius.zone_matcher');
    $zones = $zoneMatcher->matchAll($user->getAddress());

Internally, Sylius uses this service to define the shipping and billing zones of an *Order*, but you can use it for many different things and it is totally up to you.

Final Thoughts
--------------

...

Learn more
----------

* ...
