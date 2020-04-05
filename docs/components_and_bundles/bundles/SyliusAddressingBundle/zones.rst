.. rst-class:: outdated

ZoneMatcher
-----------

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

This bundle exposes the **ZoneMatcher** as ``sylius.zone_matcher`` service.

.. code-block:: php

    <?php

    $zoneMatcher = $this->get('sylius.zone_matcher');

    $zone = $zoneMatcher->match($user->getBillingAddress());
