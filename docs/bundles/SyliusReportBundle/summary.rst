Summary
=======

Configuration reference
-----------------------

.. code-block:: yaml

    sylius_report:
        driver: ~
        resources:
            report:
                classes:
                    model: Sylius\Report\Model\Report
                    interface: Sylius\Report\Model\ReportInterface
                    costroller: Sylius\ReportBundle\Controller\ReportController
                    repository: ~
                    factory:    Sylius\Resource\Factory\Factory
                    form:
                        default: Sylius\ReportBundle\Form\Type\ReportType
                validation_groups:
                    default: [ sylius ]

Tests
-----

.. code-block:: bash

    $ composer install
    $ phpunit

Working examples
----------------

If you want to see working implementation, try out the `Sylius application <http://github.com/Sylius/Sylius>`_.


Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
