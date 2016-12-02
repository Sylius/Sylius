Commands
========

Listing fixtures
----------------

To list all available suites and fixtures, use the ``sylius:fixtures:list`` command.

.. code-block:: bash

    $ bin/console sylius:fixtures:list

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

To load a suite, use the ``sylius:fixtures:load [suite]`` command.

.. code-block:: bash

    $ bin/console sylius:fixtures:load default

    Running suite "default"...
    Running fixture "country"...
    Running fixture "locale"...
    Running fixture "currency"...
