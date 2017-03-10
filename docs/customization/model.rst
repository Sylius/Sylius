Customizing Models
==================

All models in Sylius are placed in the ``Sylius\Component\*ComponentName*\Model`` namespaces alongside with their interfaces.

.. warning::
    Many models in Sylius are **extended in the Core component**.
    If the model you are willing to override exists in the ``Core`` you should be extending the ``Core`` one, not the base model from the component.

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

Let's take the ``Sylius\Component\Addressing\Country`` as an example. This one is not extended in Core.
How can you check that?

For the ``Country`` run:

.. code-block:: bash

    $ php bin/console debug:container --parameter=sylius.model.country.class

As a result you will get the ``Sylius\Component\Addressing\Model\Country`` - this is the class that you need to be extending.

Assuming that you would want to add another field on the model - for instance a ``flag``.

**1.** The first thing to do is to write your own class which will extend the base ``Country`` class.

.. code-block:: php

    <?php

    namespace AppBundle\Entity;

    use Sylius\Component\Addressing\Model\Country as BaseCountry;

    class Country extends BaseCountry
    {
        /**
         * @var bool
         */
        private $flag;

        /**
         * @return bool
         */
        public function getFlag()
        {
            return $this->flag;
        }

        /**
         * @param bool $flag
         */
        public function setFlag($flag)
        {
            $this->flag = $flag;
        }
    }

**2.** Next define your entity's mapping.

The file should be placed in ``AppBundle/Resources/config/doctrine/Country.orm.yml``

.. code-block:: yaml

    AppBundle\Entity\Country:
        type: entity
        table: sylius_country
        fields:
            flag:
                type: boolean
                nullable: true

**3.** Finally you'll need to override the model's class in the ``app/config/config.yml``.

Under the ``sylius_*`` where ``*`` is the name of the bundle of the model you are customizing, in our case it will be the ``SyliusAddressingBundle`` -> ``sylius_addressing``.

.. code-block:: yaml

    sylius_addressing:
        resources:
            country:
                classes:
                    model: AppBundle\Entity\Country

**4.** Update the database. There are two ways to do it.

* via direct database schema update:

.. code-block:: bash

    $ php bin/console doctrine:schema:update --force

* via migrations:

Which we strongly recommend over updating the schema.

.. code-block:: bash

    $ php bin/console doctrine:migrations:diff
    $ php bin/console doctrine:migrations:migrate

.. tip::

    Read more about the database modifications and migrations in the `Symfony documentation here <http://symfony.com/doc/current/book/doctrine.html#creating-the-database-tables-schema>`_.

**5.** Additionally if you want to give the administrator an ability to add the ``flag`` to any of countries,
you'll need to update its form type. Check how to do it :doc:`here </customization/form>`.

What happens while overriding Models?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

* Parameter ``sylius.model.country.class`` contains ``AppBundle\Entity\Country``.
* ``sylius.repository.country`` represents Doctrine repository for your new class.
* ``sylius.manager.country`` represents Doctrine object manager for your new class.
* ``sylius.controller.country`` represents the controller for your new class.
* All Doctrine relations to ``Sylius\Component\Addressing\Model\Country`` are using your new class as *target-entity*, you do not need to update any mappings.
* ``CountryType`` form type is using your model as ``data_class``.
* ``Sylius\Component\Addressing\Model\Country`` is automatically turned into Doctrine Mapped Superclass.

How to customize a translatable Model?
--------------------------------------

One of translatable entities in Sylius is the Shipping Method. Let's try to extend it with a new field.
Shipping methods may have different delivery time, let's save it on the ``estimatedDeliveryTime`` field.

Just like for regular models you can also check the class of translatable models like that:

.. code-block:: bash

    $ php bin/console debug:container --parameter=sylius.model.shipping_method.class

**1.** The first thing to do is to write your own class which will extend the base ``ShippingMethod`` class.

.. code-block:: php

    <?php

    namespace AppBundle\Entity;

    use Sylius\Component\Core\Model\ShippingMethod as BaseShippingMethod;
    use Sylius\Component\Shipping\Model\ShippingMethodTranslation;

    class ShippingMethod extends BaseShippingMethod
    {
        /**
         * @var string
         */
        private $estimatedDeliveryTime;

        /**
         * @return string
         */
        public function getEstimatedDeliveryTime()
        {
            return $this->estimatedDeliveryTime;
        }

        /**
         * @param string $estimatedDeliveryTime
         */
        public function setEstimatedDeliveryTime($estimatedDeliveryTime)
        {
            $this->estimatedDeliveryTime = $estimatedDeliveryTime;
        }

        /**
         * {@inheritdoc}
         */
        public static function getTranslationClass()
        {
            return ShippingMethodTranslation::class;
        }
    }

.. note::

    Remember to set the translation class properly, just like above in the ``getTranslationClass()`` method.

**2.** Next define your entity's mapping.

The file should be placed in ``AppBundle/Resources/config/doctrine/ShippingMethod.orm.yml``

.. code-block:: yaml

    AppBundle\Entity\ShippingMethod:
        type: entity
        table: sylius_shipping_method
        fields:
            estimatedDeliveryTime:
                type: string
                nullable: true

**3.** Finally you'll need to override the model's class in the ``app/config/config.yml``.

Under the ``sylius_*`` where ``*`` is the name of the bundle of the model you are customizing,
in our case it will be the ``SyliusShippingBundle`` -> ``sylius_shipping``.

.. code-block:: yaml

    sylius_shipping:
        resources:
            shipping_method:
                classes:
                    model: AppBundle\Entity\ShippingMethod

**4.** Update the database. There are two ways to do it.

* via direct database schema update:

.. code-block:: bash

    $ php bin/console doctrine:schema:update --force

* via migrations:

Which we strongly recommend over updating the schema.

.. code-block:: bash

    $ php bin/console doctrine:migrations:diff
    $ php bin/console doctrine:migrations:migrate

.. tip::

    Read more about the database modifications and migrations in the `Symfony documentation here <http://symfony.com/doc/current/book/doctrine.html#creating-the-database-tables-schema>`_.

**5.** Additionally if you need  to add the ``estimatedDeliveryTime`` to any of your shipping methods in the admin panel,
you'll need to update its form type. Check how to do it :doc:`here </customization/form>`.

.. warning::

    If you want the new field of your entity to be translatable, you need to extend the Translation class of your entity.
    In case of the ShippingMethod it would be the ``Sylius\Component\Shipping\Model\ShippingMethodTranslation``.
    Also the form on which you will add the new field should be the TranslationType.

How to customize translatable fields of a translatable Model?
-------------------------------------------------------------

Suppose you want to add a translatable property to a translatable entity, for example to the Shipping Method. Let assume that you would like the Shipping method to include a message with the delivery conditions. Let's save it on the ``deliveryConditions`` field.

Just like for regular models you can also check the class of translatable models like that:

.. code-block:: bash

    $ php bin/console debug:container --parameter=sylius.model.shipping_method_translation.class

**1.** In order to add a translatable property to your entity you need to define it on the ``AppBundle\Entity\ShippingMethodTranslation`` class of your bundle, that will extend the base ``Sylius\Component\Shipping\Model\ShippingMethodTranslation``.

.. code-block:: php

    <?php

    namespace AppBundle\Entity;

    use Sylius\Component\Shipping\Model\ShippingMethodTranslation as BaseShippingMethodTranslation;

    class ShippingMethodTranslation extends BaseShippingMethodTranslation
    {
        /**
         * @var string
         */
        private $deliveryConditions;

        /**
         * @return string
         */
        public function getDeliveryConditions()
        {
            return $this->deliveryConditions;
        }

        /**
         * @param string $deliveryConditions
         */
        public function setDeliveryConditions($deliveryConditions)
        {
            $this->deliveryConditions = $deliveryConditions;
        }
    }

**2.** Next define your entity's mapping.

The file should be placed in ``AppBundle/Resources/config/doctrine/ShippingMethodTranslation.orm.yml``

.. code-block:: yaml

    AppBundle\Entity\ShippingMethodTranslation:
        type: entity
        table: sylius_shipping_method_translation
        fields:
            deliveryConditions:
                type: string
                nullable: true

**3.** You'll need to provide access to the new fields in the ```ShippingMethod``` class and initialize the translations collection in the constructor.

.. code-block:: php

    <?php

    namespace AppBundle\Entity;

    //[...]
    use AppBundle\Entity\ShippingMethodTranslation;
    use Sylius\Component\Resource\Model\TranslatableTrait;


    class ShippingMethod extends BaseShippingMethod
    {
        //[...]

        use TranslatableTrait {
            __construct as private initializeTranslationsCollection;
        }
        
        public function __construct()
        {
            parent::__construct();
            $this->initializeTranslationsCollection();
        }

        //[...]

       /**
         * Set delivery conditions
         *
         * @param string $deliveryConditions
         */
        public function setDeliveryConditions($deliveryConditions = null)
        {
            $this->getTranslation()->setDeliveryConditions($deliveryConditions);
        }

       /**
         * Get delivery conditions
         *
         * @return string
         */
        public function getDeliveryConditions()
        {
            return $this->getTranslation()->getDeliveryConditions();
        }

       /**
         * {@inheritdoc}
         */
        public static function getTranslationClass()
        {
            return ShippingMethodTranslation::class;
        }
    }

**4.** Finally you'll need to override the model's class in the ``app/config/config.yml``.

Under the ``sylius_*`` where ``*`` is the name of the bundle of the model you are customizing,
in our case it will be the ``SyliusShippingBundle`` -> ``sylius_shipping``.

.. code-block:: yaml

    sylius_shipping:
        resources:
            shipping_method:
                classes:
                    model: AppBundle\Entity\ShippingMethod
                translation:
                    classes:
                        model: AppBundle\Entity\ShippingMethodTranslation

**5.** Update the database. There are two ways to do it.

* via direct database schema update:

.. code-block:: bash

    $ php bin/console doctrine:schema:update --force

* via migrations:

Which we strongly recommend over updating the schema.

.. code-block:: bash

    $ php bin/console doctrine:migrations:diff
    $ php bin/console doctrine:migrations:migrate

.. tip::

    Read more about the database modifications and migrations in the `Symfony documentation here <http://symfony.com/doc/current/book/doctrine.html#creating-the-database-tables-schema>`_.

**6.** Additionally if you need  to add the ``deliveryConditions`` to any of your shipping methods in the admin panel,
you'll need to update its form type. Check how to do it :doc:`here </customization/form>`.
