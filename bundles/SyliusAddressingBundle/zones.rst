Zones
=====

...

ZoneMatcher
-----------

Since zones are usually used for tax and shipping calculations, you can use this service for getting best matching zone for given address.
Then you can apply tax calculation for matched zone.

.. code-block:: php

    <?php

    // ...
    $zoneMatcher = $this->get('sylius_addressing.zone_matcher');

    $zone = $zoneMatcher->match($address);
