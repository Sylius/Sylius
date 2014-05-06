The Address
===========

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
