Customizing Models
==================

All models in Sylius are placed in the ``Sylius\Component\*ComponentName*\Model`` namespaces alongside with their interfaces.

.. warning::

    Many models in Sylius are **extended in the Core component**.
    If the model you are willing to override exists in the ``Core`` you should be extending the ``Core`` one, not the base model from the component.

.. warning::

    Removing the generated and executed doctrine migration may cause warnings while a new migration is executed.
    To avoid it, we suggest you do not delete the migration.

.. note::

    Note that there are **translatable models** in Sylius also. The guide to translatable entities can be found below the regular one.

Why would you customize a Model?
--------------------------------

To give you an idea of some purposes of models customizing have a look at a few examples:

* Add ``flag`` field to the ``Country``
* Add ``secondNumber`` to the ``Customer``
* Change the ``reviewSubject`` of a ``Review`` (in Sylius we have ``ProductReviews`` but you can imagine for instance a ``CustomerReview``)
* Add ``icon`` to the ``PaymentMethod``

And of course many similar operations limited only by your imagination.

Let's now see how you should perform such customizations.

How to customize a Model?
-------------------------

.. tip::

    You can browse the full implementation of this example on `this GitHub Pull Request <https://github.com/Sylius/Customizations/pull/2>`__.

Let's take the ``Sylius\Component\Addressing\Country`` as an example. This one is not extended in Core.
How can you check that?

For the ``Country`` run:

.. code-block:: bash

    php bin/console debug:container --parameter=sylius.model.country.class

As a result you will get the ``Sylius\Component\Addressing\Model\Country`` - this is the class that you need to be extending.

Assuming that you would want to add another field on the model - for instance a ``flag``, where the flag is a variable that stores your image URL

**1.** The first thing to do is to add your field to the ``App\Entity\Addressing\Country`` class, which extends the base ``Sylius\Component\Addressing\Model\Country`` class.

Apply the following changes to the ``src/Entity/Addressing/Country.php`` file that already exists in Sylius-Standard.

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Entity\Addressing;

    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Addressing\Model\Country as BaseCountry;
    use Sylius\Component\Addressing\Model\CountryInterface;

    /**
     * @ORM\Entity()
     * @ORM\Table(name="sylius_country")
     */
    class Country extends BaseCountry implements CountryInterface
    {
        /** @ORM\Column(type="string", nullable=true) */
        private $flag;

        public function getFlag(): ?string
        {
            return $this->flag;
        }

        public function setFlag(string $flag): void
        {
            $this->flag = $flag;
        }
    }

**2.** After that you'll need to check the model's class in the ``config/packages/_sylius.yaml``.

Under the ``sylius_*`` where ``*`` is the name of the bundle of the model you are customizing, in our case it will be the ``SyliusAddressingBundle`` -> ``sylius_addressing``.

That in Sylius-Standard configuration is overridden already.

.. code-block:: yaml

    sylius_addressing:
        resources:
            country:
                classes:
                    model: App\Entity\Addressing\Country

You can check if the configuration in ``config/_sylius.yaml`` is correct by running:

.. code-block:: bash

    php bin/console debug:container --parameter=sylius.model.country.class

If all is well the output should look like:

.. code-block:: bash

    ---------------------------- -------------------------------------------
     Parameter                    Value
    ---------------------------- -------------------------------------------
     sylius.model.country.class   App\Entity\Addressing\Country
    ---------------------------- -------------------------------------------

.. tip::

    In some cases you will see an error stating that there is something wrong with the resource configuration:
    ``Unrecognized option "classes" under...``
    When this happens, please refer to :ref:`resource-configuration`.

**3.** Update the database. There are two ways to do it.

* via direct database schema update:

.. code-block:: bash

    php bin/console doctrine:schema:update --force

* via migrations:

Which we strongly recommend over updating the schema.

.. code-block:: bash

    php bin/console doctrine:migrations:diff
    php bin/console doctrine:migrations:migrate

.. tip::

    Read more about the database modifications and migrations in the `Symfony documentation here <https://symfony.com/doc/current/book/doctrine.html#creating-the-database-tables-schema>`_.

**4.** Additionally if you want to give the administrator an ability to add the ``flag`` to any of countries,
you'll need to update its form type. Check how to do it :doc:`here </customization/form>`.

What happens while overriding Models?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

* Parameter ``sylius.model.country.class`` contains ``App\Entity\Addressing\Country``.
* ``sylius.repository.country`` represents Doctrine repository for your new class.
* ``sylius.manager.country`` represents Doctrine object manager for your new class.
* ``sylius.controller.country`` represents the controller for your new class.
* All Doctrine relations to ``Sylius\Component\Addressing\Model\Country`` are using your new class as *target-entity*, you do not need to update any mappings.
* ``CountryType`` form type is using your model as ``data_class``.
* ``Sylius\Component\Addressing\Model\Country`` is automatically turned into Doctrine Mapped Superclass.

How to customize a translatable Model?
--------------------------------------

.. tip::

    You can browse the full implementation of this example on `this GitHub Pull Request <https://github.com/Sylius/Customizations/pull/4>`__.

One of translatable entities in Sylius is the Shipping Method. Let's try to extend it with a new field.
Shipping methods may have different delivery time, let's save it on the ``estimatedDeliveryTime`` field.

Just like for regular models you can also check the class of translatable models like that:

.. code-block:: bash

    php bin/console debug:container --parameter=sylius.model.shipping_method.class

**1.** The first thing to do is to add your own fields in class ``App\Entity\Shipping\ShippingMethod`` extending the base ``Sylius\Component\Core\Model\ShippingMethod`` class.


Apply the following changes to the ``src/Entity/Shipping/ShippingMethod.php`` file existing in Sylius-Standard.


.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Entity\Shipping;

    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Core\Model\ShippingMethod as BaseShippingMethod;
    use Sylius\Component\Core\Model\ShippingMethodInterface;
    use Sylius\Component\Shipping\Model\ShippingMethodTranslationInterface;

    /**
     * @ORM\Entity()
     * @ORM\Table(name="sylius_shipping_method")
     */
    class ShippingMethod extends BaseShippingMethod implements ShippingMethodInterface
    {
        /** @ORM\Column(type="string",nullable=true) */
        private $estimatedDeliveryTime;

        public function getEstimatedDeliveryTime(): ?string
        {
            return $this->estimatedDeliveryTime;
        }

        public function setEstimatedDeliveryTime(?string $estimatedDeliveryTime): void
        {
            $this->estimatedDeliveryTime = $estimatedDeliveryTime;
        }

        protected function createTranslation(): ShippingMethodTranslationInterface
        {
            return new ShippingMethodTranslation();
        }
    }

.. note::

    Remember to set the translation class properly, just like above in the ``createTranslation()`` method.

**2.** After that you’ll need to check the model’s class in the ``config/packages/_sylius.yaml``.

Under the ``sylius_*`` where ``*`` is the name of the bundle of the model you are customizing,
in our case it will be the ``SyliusShippingBundle`` -> ``sylius_shipping``.

That in Sylius-Standard configuration is overridden already, but you may check if it is correctly overridden.

.. code-block:: yaml

    sylius_shipping:
        resources:
            shipping_method:
                classes:
                    model: App\Entity\Shipping\ShippingMethod

Configuration ``sylius_shipping:`` is provided by default in the sylius-standard

**3.** Update the database. There are two ways to do it.

* via direct database schema update:

.. code-block:: bash

    php bin/console doctrine:schema:update --force

* via migrations:

Which we strongly recommend over updating the schema.

.. code-block:: bash

    php bin/console doctrine:migrations:diff
    php bin/console doctrine:migrations:migrate

.. tip::

    Read more about the database modifications and migrations in the `Symfony documentation here <https://symfony.com/doc/current/book/doctrine.html#creating-the-database-tables-schema>`_.

**4.** Additionally if you need  to add the ``estimatedDeliveryTime`` to any of your shipping methods in the admin panel,
you'll need to update its form type. Check how to do it :doc:`here </customization/form>`.

.. warning::

    If you want the new field of your entity to be translatable, you need to extend the Translation class of your entity.
    In case of the ShippingMethod it would be the ``Sylius\Component\Shipping\Model\ShippingMethodTranslation``.
    Also the form on which you will add the new field should be the TranslationType.

How to customize translatable fields of a translatable Model?
-------------------------------------------------------------

.. tip::

    You can browse the full implementation of this example on `this GitHub Pull Request <https://github.com/Sylius/Customizations/pull/7>`__.

Suppose you want to add a translatable property to a translatable entity, for example to the Shipping Method.
Let's assume that you would like the Shipping method to include a message with the delivery conditions. Let's save it on the ``deliveryConditions`` field.

Just like for regular models you can also check the class of translatable models like that:

.. code-block:: bash

    php bin/console debug:container --parameter=sylius.model.shipping_method_translation.class

**1.** In order to add a translatable property to your entity, start from defining it on the class `App\Entity\Shipping\ShippingMethodTranslation` is already there in the right place.

Apply the following changes to the ``src/Entity/Shipping/ShippingMethodTranslation.php`` file existing in Sylius-Standard.

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Entity\Shipping;

    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Shipping\Model\ShippingMethodTranslation as BaseShippingMethodTranslation;
    use Sylius\Component\Shipping\Model\ShippingMethodTranslationInterface;

    /**
     * @ORM\Entity()
     * @ORM\Table(name="sylius_shipping_method_translation")
     */
    class ShippingMethodTranslation extends BaseShippingMethodTranslation implements ShippingMethodTranslationInterface
    {
        /** @ORM\Column(type="string", nullable=true) */
        private $deliveryConditions;

        public function getDeliveryConditions(): ?string
        {
            return $this->deliveryConditions;
        }

        public function setDeliveryConditions(?string $deliveryConditions): void
        {
            $this->deliveryConditions = $deliveryConditions;
        }
    }

**2.** Implement the getter and setter methods of the interface on the ``App\Entity\Shipping\ShippingMethod`` class.

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Entity\Shipping;

    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Core\Model\ShippingMethod as BaseShippingMethod;
    use Sylius\Component\Core\Model\ShippingMethodInterface;
    use Sylius\Component\Shipping\Model\ShippingMethodTranslationInterface;

    /**
     * @ORM\Entity()
     * @ORM\Table(name="sylius_shipping_method")
     */
    class ShippingMethod extends BaseShippingMethod implements ShippingMethodInterface
    {
        public function getDeliveryConditions(): ?string
        {
            return $this->getTranslation()->getDeliveryConditions();
        }

        public function setDeliveryConditions(?string $deliveryConditions): void
        {
            $this->getTranslation()->setDeliveryConditions($deliveryConditions);
        }

        protected function createTranslation(): ShippingMethodTranslationInterface
        {
            return new ShippingMethodTranslation();
        }
    }

.. note::

    Remember that if the original entity is not translatable you will need to initialize the translations collection in the constructor,
    and use the TranslatableTrait. Take a careful look at the Sylius translatable entities.

**3.** After that you'll need to override the model's class in the ``config/packages/_sylius.yaml``.

Under the ``sylius_*`` where ``*`` is the name of the bundle of the model you are customizing,
in our case it will be the ``SyliusShippingBundle`` -> ``sylius_shipping``.

.. code-block:: yaml

    sylius_shipping:
        resources:
            shipping_method:
                classes:
                    model: App\Entity\Shipping\ShippingMethod
                translation:
                    classes:
                        model: App\Entity\Shipping\ShippingMethodTranslation

Configuration ``sylius_addressing:`` is provided by default in the sylius-standard

**4.** Update the database. There are two ways to do it.

* via direct database schema update:

.. code-block:: bash

    php bin/console doctrine:schema:update --force

* via migrations:

Which we strongly recommend over updating the schema.

.. code-block:: bash

    php bin/console doctrine:migrations:diff
    php bin/console doctrine:migrations:migrate

.. tip::

    Read more about the database modifications and migrations in the `Symfony documentation here <https://symfony.com/doc/current/book/doctrine.html#creating-the-database-tables-schema>`_.

**5.** If you need to add delivery conditions to your shipping methods in the admin panel,
you'll need to update its form type. Check how to do it :doc:`here </customization/form>`.

.. include:: /customization/plugins.rst

Learn more
----------

* `Sylius Database Schema <https://drawsql.app/templates/sylius>`_
