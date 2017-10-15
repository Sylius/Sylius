Basic Usage
===========

.. _component_addressing_matcher_zone-matcher:

ZoneMatcher
-----------

Zones are not very useful by themselves, but they can take part in e.g. a complex taxation/shipping
system. This service is capable of getting a :ref:`component_addressing_model_zone`
specific for given :ref:`component_addressing_model_address`.

It uses a collaborator implementing Doctrine's
`ObjectRepository`_ interface to obtain all available zones,
compare them with given :ref:`component_addressing_model_address`
and return best fitted :ref:`component_addressing_model_zone`.

.. _ObjectRepository: http://www.doctrine-project.org/api/common/2.4/class-Doctrine.Common.Persistence.ObjectRepository.html

First lets make some preparations.

.. code-block:: php

   <?php

   use Sylius\Component\Addressing\Model\Address;
   use Sylius\Component\Addressing\Model\Zone;
   use Sylius\Component\Addressing\Model\ZoneInterface;
   use Sylius\Component\Addressing\Model\ZoneMember;
   use Sylius\Component\Resource\Repository\InMemoryRepository;

   $zoneRepository = new InMemoryRepository(ZoneInterface::class);
   $zone = new Zone();
   $zoneMember = new ZoneMember();

   $address = new Address();
   $address->setCountry('US');

   $zoneMember->setCode('US');
   $zoneMember->setBelongsTo($zone);

   $zone->addMember($zoneMember);

   $zoneRepository->add($zone);

Now that we have all the needed parts lets match something.

.. code-block:: php

   <?php

   use Sylius\Component\Addressing\Matcher\ZoneMatcher;

   $zoneMatcher = new ZoneMatcher($zoneRepository);

   $zoneMatcher->match($address); // returns the best matching zone
                                  // for the address given, in this case $zone

**ZoneMatcher** can also return all zones containing given :ref:`component_addressing_model_address`.

.. code-block:: php

   <?php

   $zoneMatcher->matchAll($address); // returns all zones containing given $address

To be more specific you can provide a ``scope`` which will
narrow the search only to zones with same corresponding property.

.. code-block:: php

   <?php

   $zone->setScope('earth');

   $zoneMatcher->match($address, 'earth'); // returns $zone
   $zoneMatcher->matchAll($address, 'mars'); // returns null as there is no
                                             // zone with 'mars' scope

.. note::
   This service implements the :ref:`component_addressing_matcher_zone-matcher-interface`.

.. caution::
   Throws `\\InvalidArgumentException`_.

.. _\\InvalidArgumentException: http://php.net/manual/en/class.invalidargumentexception.php
