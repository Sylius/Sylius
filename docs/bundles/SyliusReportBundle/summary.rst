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
                    model: Sylius\Component\Report\Model\Report
                    interface: Sylius\Component\Report\Model\ReportInterface
                    costroller: Sylius\Bundle\ReportBundle\Controller\ReportController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\ReportBundle\Form\Type\ReportType
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
