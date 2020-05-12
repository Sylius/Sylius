Customizing Fixture Suites
==========================

What are fixture suites?
~~~~~~~~~~~~~~~~~~~~~~~~

Suites are predefined groups of fixtures that can be run together. For example, they can be full shop configurations for manual tests purposes.

Why would you customize fixture suites?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    * tailoring the default Sylius fixture suite to your needs (removing Orders for example)
    * creating your own fixture suite

How to use suites?
~~~~~~~~~~~~~~~~~~

Complete list of suites can be shown with the ``bin/console sylius:fixtures:list`` command.
The ``default`` suite is loaded if ``bin/console sylius:fixtures:load`` command
is executed without any additional argument. If you are creating a new suite you must use this command providing the
name of your suite as an argument: ``bin/console sylius:fixtures:load your_custom_suite``.

How to create custom fixture suites?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. tip::

    You can browse the full implementation of this example on `this GitHub Pull Request
    <https://github.com/Sylius/Customizations/pull/24>`__.

.. tip::

    If you want to create your fixtures with different locale than ``en_US`` you must change the ``locale`` parameter in ``config/services.yaml``.

    .. code-block:: yaml

        parameters:
            locale: pl_PL

**1.** Create the ``config/packages/sylius_fixtures.yaml`` file and add the following code there:

.. code-block:: yaml

    sylius_fixtures:
        suites:
            poland: # custom suite's name
                fixtures:
                    currency:
                        options:
                            currencies: ['PLN'] # add desired currencies as an array

                    geographical: # Countries, provinces and zones available in your store
                        options:
                            countries:
                                - "PL"
                            zones:
                                PL:
                                    name: "Poland"
                                    countries:
                                        - "PL"

                    channel:
                        options:
                            custom:
                                pl_web_store:
                                    name: "PL Web Store"
                                    code: "PL_WEB"
                                    locales: # choose the locale for this channel
                                        - "%locale%"
                                    currencies: # choose currencies for this channel
                                        - "PLN"
                                    enabled: true
                                    hostname: "localhost"

                    shipping_method: # create shipping methods and choose channels in which it is available
                        options:
                            custom:
                                inpost:
                                    code: "inpost"
                                    name: "InPost"
                                    channels:
                                        - "PL_WEB"
                                    zone: "PL"

**2.** Load your custom suite with ``bin/console sylius:fixtures:load poland`` command.


.. tip::

    By default, a new fixture suite will not purge your database. If you want to run it always on a clear database,
    add the ``orm_purger`` listener under your custom suite name:

    .. code-block:: yaml

        sylius_fixtures:
            suites:
                poland:
                    listeners:
                        orm_purger: ~

Learn more
----------

* :doc:`The Book: Fixtures </book/architecture/fixtures>`
* `FixturesBundle <https://github.com/Sylius/SyliusFixturesBundle/blob/master/docs/index.md>`_
