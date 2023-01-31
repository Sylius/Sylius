.. rst-class:: outdated

Summary
=======

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

Configuration reference
-----------------------

.. code-block:: yaml

    sylius_user:
        driver: doctrine/orm
        encoder: argon2i
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
                    encoder: null
                    login_tracking_interval: null
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
                    encoder: null
                    login_tracking_interval: null
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
                    encoder: false
                    login_tracking_interval: null
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

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
