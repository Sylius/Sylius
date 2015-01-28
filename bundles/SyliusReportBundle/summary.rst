Summary
=======

Configuration reference
-----------------------

.. code-block:: yaml

    sylius_report:
        driver: ~
        classes:
            report:
                model: Sylius\Component\Report\Model\Report
                costroller: Sylius\Bundle\ReportBundle\Controller\ReportController
                repository: ~
                form: Sylius\Bundle\ReportBundle\Form\Type\ReportType

Tests
-----

.. code-block:: bash

    $ composer install --dev --prefer-dist
    $ phpunit

Working examples
----------------

If you want to see working implementation, try out the `Sylius application <http://github.com/Sylius/Sylius>`_.


Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
