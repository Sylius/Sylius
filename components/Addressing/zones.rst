Zones and Zone Matching
=======================

This library allows you to define **Zones**, which represent a specific geographical area.

Zone Model
----------

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

It exposes the following API:

.. code-block:: php

    <?php

    $zone->getName(); // Name of the zone, for example "EU".
    $zone->getType(); // Type, for example "country".

    foreach ($zone->getMembers() as $member) {
        echo $member->getName();
    }

    $zone->getCreatedAt();
    $zone->getUpdatedAt();

Three different types of zones are supported out-of-the-box.

* `country` zone, which consists of many countries.
* `province` zone, which is constructed from multiple provinces.
* `zone`, which is a group of other zones.

Each zone type has different **ZoneMember** model, but they all expose the same API:

.. code-block:: php

    <?php

    foreach ($zone->getMembers() as $member) {
        echo $member->getName();

        echo $member->getZone()->getName(); // Name of the zone.
    }

There are following models and each of them represents a different zone member:

* ZoneMemberCountry
* ZoneMemberProvince
* ZoneMemberZone

Each **ZoneMember** instance holds a reference to the **Zone** object and appropriate area entity, for example a **Country**.

Creating a zone requires adding appropriate members:

.. code-block:: php

    <?php

    use Sylius\Component\Addressing\Country;
    use Sylius\Component\Addressing\Zone;
    use Sylius\Component\Addressing\ZoneInterface;
    use Sylius\Component\Addressing\ZoneMemberCountry;

    $eu = new Zone();
    $eu
        ->setName('European Union')
        ->setType(ZoneInterface::TYPE_COUNTRY)
    ;

    $germany = new Country();
    $germany
        ->setName('Germany')
        ->setIsoName('DE')
    ;
    $france = new Country();
    $france
        ->setName('France')
        ->setIsoName('FR')
    ;
    $poland = new Country();
    $poland
        ->setName('Poland')
        ->setIsoName('PL')
    ;

    $germanyMember = new ZoneMemberCountry();
    $germanyMember->setCountry($germany)

    $franceMember = new ZoneMemberCountry();
    $franceMember->setCountry($france)

    $polandMember = new ZoneMemberCountry();
    $polandMember->setCountry($poland)

    $eu
        ->addMember($germany)
        ->addMember($france)
        ->addMember($poland)
    ;

.. tip::

    Default zone types are defined as constants in the ``ZoneInterface`` interface.

Exactly the same process applies to different types of Zones.

Matching a Zone
---------------

Zones are not very useful by themselves, but they can be part of a complex taxation/shipping or any other system.
A service implementing the `ZoneMatcherInterface` is responsible for matching the **Address** to a specific **Zone**.

Default implementation uses a collaborator implementing ``RepositoryInterface`` to obtain all available zones and then compares them with the given **Address**.

.. code-block:: php

    <?php

    use Sylius\Component\Addressing\Matcher\ZoneMatcher;

    $zoneRepository = // Get the repository.
    $zoneMatcher = new ZoneMatcher($zoneRepository);

    $zone = $zoneMatcher->match($user->getAddress());

    // Apply appropriate taxes or return shipping methods supported for given zone.

Zone matcher can also return all matching zones. (not only the best one)

.. code-block:: php

    <?php

    $zones = $zoneMatcher->matchAll($user->getAddress());

    // Inventory can be take from stock locations in the following zones...

There are many other use-cases!
