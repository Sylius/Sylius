Models
======

The Address
-----------

This is a very basic representation of Address model.

+-----------+--------------------------------+
| Attribute | Description                    |
+===========+================================+
| id        | Unique id of the address       |
+-----------+--------------------------------+
| firstname |                                |
+-----------+--------------------------------+
| lastname  |                                |
+-----------+--------------------------------+
| company   |                                |
+-----------+--------------------------------+
| country   | Reference to Country model     |
+-----------+--------------------------------+
| province  | Reference to Province model    |
+-----------+--------------------------------+
| street    |                                |
+-----------+--------------------------------+
| city      |                                |
+-----------+--------------------------------+
| postcode  |                                |
+-----------+--------------------------------+
| createdAt | Date when address was created  |
+-----------+--------------------------------+
| updatedAt | Date of last address update    |
+-----------+--------------------------------+

Creating and modifying Address object is very simple.

.. code-block:: php

    <?php

    use Sylius\Component\Addressing\Address;

    $address = new Address();

    $address
        ->setFirstname('John')
        ->setLastname('Doe')
        ->setStreet('Superstreet 14')
        ->setCity('New York')
        ->setPostcode('13111')
    ;

    $user = // Get your user from somewhere or any model which can reference an Address.
    $user->setShippingAddress($address);

Country
-------

"Country" model represents a geographical area of a country.

+-----------+--------------------------------+
| Attribute | Description                    |
+===========+================================+
| id        | Unique id of the country       |
+-----------+--------------------------------+
| name      |                                |
+-----------+--------------------------------+
| isoName   |                                |
+-----------+--------------------------------+
| provinces | Collection of provinces        |
+-----------+--------------------------------+
| createdAt | Date when country was created  |
+-----------+--------------------------------+
| updatedAt | Date of last country update    |
+-----------+--------------------------------+

.. code-block:: php

    <?php

    use Sylius\Component\Addressing\Address;
    use Sylius\Component\Addressing\Country;
    
    $poland = new Country();
    $poland
        ->setName('Poland')
        ->setIsoName('PL')
    ;

    $address
        ->setFirstname('Pawel')
        ->setLastname('Jedrzejewski')
        ->setCountry($poland)
        ->setStreet('Testing 123')
        ->setCity('Lodz')
        ->setPostcode('21-242')
    ;

Province
--------

"Province" is a smaller area inside of a Country. You can use it to manage provinces or states and assign it to an address as well.

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

.. code-block:: php

    <?php

    use Sylius\Component\Addressing\Address;
    use Sylius\Component\Addressing\Country;
    use Sylius\Component\Addressing\Province;

    $usa = new Country();
    $usa
        ->setName('United States of America')
        ->setIsoName('US')
    ;

    $tennessee = new Province();
    $tennessee->setName('Tennessee');

    $address
        ->setFirstname('John')
        ->setLastname('Deo')
        ->setCountry($usa)
        ->setProvince($tennessee)
        ->setStreet('Testing 111')
        ->setCity('Nashville')
        ->setPostcode('123123')
    ;

Zones
-----

This library allows you to define **Zones**, which represent a specific geographical area.

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

* ``country`` zone, which consists of many countries.
* ``province`` zone, which is constructed from multiple provinces.
* ``zone``, which is a group of other zones.

Each zone type has different **ZoneMember** model, but they all expose the same API:

.. code-block:: php

    <?php

    foreach ($zone->getMembers() as $member) {
        echo $member->getName();

        echo $member->getZone()->getName(); // Name of the zone.
    }

There are following models and each of them represents a different zone member:

* ``ZoneMemberCountry``
* ``ZoneMemberProvince``
* ``ZoneMemberZone``

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
