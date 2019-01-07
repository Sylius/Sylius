Summary
=======

.. note::

    To be written.

Configuration reference
-----------------------

.. code-block:: yaml

    sylius_user:
        driver: doctrine/orm
        resources:
            admin:
                user:
                    classes:
                        model: Sylius\Component\Core\Model\AdminUser
                        repository: Sylius\Bundle\UserBundle\Doctrine\ORM\UserRepository
                        form: Sylius\Bundle\CoreBundle\Form\Type\User\AdminUserType
                        interface: Sylius\Component\User\Model\UserInterface
                        controller: Sylius\Bundle\UserBundle\Controller\UserController
                        factory: Sylius\Component\Resource\Factory\Factory
                    templates: 'SyliusUserBundle:User'
                    resetting:
                        token:
                            ttl: P1D
                            length: 16
                            field_name: passwordResetToken
                        pin:
                            length: 4
                            field_name: passwordResetToken
                    verification:
                        token:
                            length: 16
                            field_name: emailVerificationToken
            shop:
                user:
                    classes:
                        model: Sylius\Component\Core\Model\ShopUser
                        repository: Sylius\Bundle\CoreBundle\Doctrine\ORM\UserRepository
                        form: Sylius\Bundle\CoreBundle\Form\Type\User\ShopUserType
                        interface: Sylius\Component\User\Model\UserInterface
                        controller: Sylius\Bundle\UserBundle\Controller\UserController
                        factory: Sylius\Component\Resource\Factory\Factory
                    templates: 'SyliusUserBundle:User'
                    resetting:
                        token:
                            ttl: P1D
                            length: 16
                            field_name: passwordResetToken
                        pin:
                            length: 4
                            field_name: passwordResetToken
                    verification:
                        token:
                            length: 16
                            field_name: emailVerificationToken
            oauth:
                user:
                    classes:
                        model: Sylius\Component\User\Model\UserOAuth
                        interface: Sylius\Component\User\Model\UserOAuthInterface
                        controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                        factory: Sylius\Component\Resource\Factory\Factory
                        form: Sylius\Bundle\UserBundle\Form\Type\UserType
                    templates: 'SyliusUserBundle:User'
                    resetting:
                        token:
                            ttl: P1D
                            length: 16
                            field_name: passwordResetToken
                        pin:
                            length: 4
                            field_name: passwordResetToken
                    verification:
                        token:
                            length: 16
                            field_name: emailVerificationToken


`phpspec2 <http://phpspec.net>`_ examples
-----------------------------------------

.. code-block:: bash

    $ composer install
    $ bin/phpspec run -fpretty --verbose

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
