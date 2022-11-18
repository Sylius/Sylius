Dates
=====

Providing Date
--------------

Retrieving the date can be achieved by calling service with implemented ``Sylius\Calendar\Provider\DateTimeProviderInterface``

.. code-block:: php

    <?php

    // some code

    class MyClass
    {
        public function __construct(protected DateTimeProviderInterface $calendar) {}

        public function myAction(): void
        {
            $dateNow = $this->$calendar->now();

            // rest of action
        }
    }
