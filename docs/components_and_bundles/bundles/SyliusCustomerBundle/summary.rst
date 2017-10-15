Summary
=======

.. note::

    To be written.

Configuration reference
-----------------------

.. code-block:: yaml

    sylius_customer:
        driver: doctrine/orm
        resources:
            customer:
                classes:
                    model: Sylius\Component\Core\Model\Customer
                    repository: Sylius\Bundle\CoreBundle\Doctrine\ORM\CustomerRepository
                    form:
                        default: Sylius\Bundle\CoreBundle\Form\Type\Customer\CustomerType
                        profile: Sylius\Bundle\CustomerBundle\Form\Type\CustomerProfileType
                        choice: Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType
                    interface: Sylius\Component\Customer\Model\CustomerInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    factory: Sylius\Component\Resource\Factory\Factory
            customer_group:
                classes:
                    model: Sylius\Component\Customer\Model\CustomerGroup
                    interface: Sylius\Component\Customer\Model\CustomerGroupInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    factory: Sylius\Component\Resource\Factory\Factory
                    form: Sylius\Bundle\CustomerBundle\Form\Type\CustomerGroupType

`phpspec2 <http://phpspec.net>`_ examples
-----------------------------------------

.. code-block:: bash

    $ composer install
    $ bin/phpspec run -fpretty --verbose

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
