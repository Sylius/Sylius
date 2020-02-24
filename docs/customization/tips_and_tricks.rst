Tips & Tricks
=============

.. _resource-configuration:

How to get Sylius Resource configuration from the container?
------------------------------------------------------------

There are some exceptions to the instructions of customizing models. In most cases the instructions
will get you exactly where you need to be, but when for example attempting to customize the ``ShopUser``
model, you will see an error:

.. code-block:: bash

    In ArrayNode.php line 331:

    Unrecognized option "classes" under "sylius_user.resources.user". Available option is "user".

In this case, when customizing the ``ShopUser`` model and using the following resource configuration:

.. code-block:: yaml

    sylius_user:
        resources:
            user:
                classes:
                    model: App\Entity\ShopUser

The error is displayed because the user entity is extended multiple times in the user bundle.
To find out the correct configuration, please run the following command:

.. code-block:: bash

    bin/console debug:config SyliusUserBundle

The output of that command should look similar to:

.. code-block:: bash

    Current configuration for "SyliusUserBundle"
    ============================================

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
                        ...
            shop:
                user:
                    classes:
                        model: Sylius\Component\Core\Model\ShopUser
                        repository: Sylius\Bundle\CoreBundle\Doctrine\ORM\UserRepository
                        form: Sylius\Bundle\CoreBundle\Form\Type\User\ShopUserType
                        interface: Sylius\Component\User\Model\UserInterface
                        controller: Sylius\Bundle\UserBundle\Controller\UserController
                        factory: Sylius\Component\Resource\Factory\Factory
                        ...
            oauth:
                user:
                    classes:
                        model: Sylius\Component\User\Model\UserOAuth
                        interface: Sylius\Component\User\Model\UserOAuthInterface
                        controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                        factory: Sylius\Component\Resource\Factory\Factory
                        ...

As you can see there is an extra layer in the configuration here.
Since in this example we're attempting to customize the ``ShopUser`` entity, we need to use the following
configuration in ``config/packages/_sylius.yaml``:

.. code-block:: yaml

    sylius_user:
        resources:
            shop:
                user:
                    classes:
                        model: App\Entity\ShopUser

This is how you should always be able to find out the correct configuration.
