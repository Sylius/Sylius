Commands
========

Listing fixtures
----------------

To list all available suites and fixtures, use ``sylius:fixtures:list`` command.

.. code-block:: bash

    $ app/console sylius:fixtures:list

    Available suites:
     - default
     - dev
     - test
    Available fixtures:
     - country
     - locale
     - currency

Loading fixtures
----------------

To load a suite, use ``sylius:fixtures:load [suite]`` command.

.. code-block:: bash

    $ app/console sylius:fixtures:load default

    Running suite "default"...
    Running fixture "country"...
    Running fixture "locale"...
    Running fixture "currency"...
