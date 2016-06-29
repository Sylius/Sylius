Architecture
============

Flexibility is one of the key concepts of **SyliusFixturesBundle**. This article aims to explain what design decisions
were made in order to achieve it.

Suites
------

Suites are collections of configured fixtures. They allow you to define different sets (for example - ``staging``,
``development`` or ``big_shop``) that can be loaded independently. They are defined through YAML configuration:

.. code-block:: yaml

    sylius_fixtures:
        suites:
            my_suite_name: # Suite name as a key
                listeners: ~
                fixtures: ~

Fixtures
--------

Fixtures are just plain old PHP objects, that change system state during their execution - they can either
persist some entities in the database, upload some files, dispatch some events or do anything you think is needed.

.. code-block:: yaml

    sylius_fixtures:
        suites:
            my_suite_name:
                fixtures:
                    my_fixture: # Fixture name as a key
                        priority: 0 # The higher priority is, the sooner the fixture will be executed
                        options: ~ # Fixture options

They implement the ``Sylius\FixturesBundle\Fixture\FixtureInterface`` and need to be registered under
the ``sylius_fixtures.fixture`` tag in order to be used in suite configuration.

.. note::

    The former interface extends the ``ConfigurationInterface``, which is widely known from ``Configuration`` classes
    placed under ``DependencyInjection`` directory in Symfony bundles.


Listeners
---------

Listeners allow you to execute code at some point of fixtures loading.

.. code-block:: yaml

    sylius_fixtures:
        suites:
            my_suite_name:
                listeners:
                    my_listener: # Listener name as a key
                        priority: 0 # The higher priority is, the sooner the fixture will be executed
                        options: ~ # Listener options

They implement at least one of four interfaces:

     - ``Sylius\FixturesBundle\Listener\BeforeSuiteListenerInterface`` - receives ``Sylius\FixturesBundle\Listener\SuiteEvent`` as an arugment
     - ``Sylius\FixturesBundle\Listener\BeforeFixtureListenerInterface``  - receives ``Sylius\FixturesBundle\Listener\FixtureEvent`` as an arugment
     - ``Sylius\FixturesBundle\Listener\AfterFixtureListenerInterface`` - receives ``Sylius\FixturesBundle\Listener\FixtureEvent`` as an arugment
     - ``Sylius\FixturesBundle\Listener\AfterSuiteListenerInterface``  - receives ``Sylius\FixturesBundle\Listener\SuiteEvent`` as an arugment

.. note::

    The former interface extends the ``ConfigurationInterface``, which is widely known from ``Configuration`` classes
    placed under ``DependencyInjection`` directory in Symfony bundles.

In order to be used in suite configuration, they need to be registered under the ``sylius_fixtures.listener``.
