Matching a Zone
===============

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
