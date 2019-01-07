ZoneMatcher
-----------

This bundle exposes the **ZoneMatcher** as ``sylius.zone_matcher`` service.

.. code-block:: php

    <?php

    $zoneMatcher = $this->get('sylius.zone_matcher');

    $zone = $zoneMatcher->match($user->getBillingAddress());
