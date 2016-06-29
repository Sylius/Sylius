Customizing Models
==================

All models in Sylius are placed in the ``Sylius\Component\*ComponentName*\Model`` namespaces alongside with their interfaces.

.. warning::
    Many models in Sylius are **extended in the Core component**.
    If the model you are willing to override exists in the ``Core`` your should be extending the ``Core`` one, not the base model from the component.

Why would you customize a Model?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To give you an idea of some purposes of models customizing have a look at a few examples:

* Add ``flag`` field to the ``Country``
* Add ``secondNumber`` to the ``Customer``
* Change the ``reviewSubject`` of a ``Review`` (in Sylius we have ``ProductReviews`` but you can imagine for instance a ``CustomerReview``)
* Add ``icon`` to the ``PaymentMethod``

And of course many similar operations limited only by your imagination.
Let's now see how you should perform such customizations.

How to customize a Model?
~~~~~~~~~~~~~~~~~~~~~~~~~

Let's take the ``Sylius\Addressing\Country`` as an example. This one is not extended in Core.
How can you check that?

For the ``Country`` run:

.. code-block:: bash

    $ php app/console debug:container --parameter=sylius.model.country.class

As a result you will get the ``Sylius\Addressing\Model\Country`` - this is the class that you need to be extending.

Assuming that you would want to add another field on the model - for instance a ``flag``.

1. The first thing to do is to write your own class which will extend the base `Country`` class.

.. code-block:: php

    <?php

    namespace AppBundle\Entity;

    use Sylius\Addressing\Model\Country as BaseCountry;

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

2. Next define your entity's mapping:

The file should be placed in ``AppBundle/Resources/config/doctrine/Country.orm.yml``

.. code-block:: yaml

    AppBundle\Entity\Country:
        type: entity
        table: sylius_country
        fields:
            flag:
                type: boolean
                nullable: true

3. Finally you'll need to override the model's class in the ``app/config/config.yml``.

.. code-block:: yaml

    sylius_addressing:
        resources:
            country:
                classes:
                    model: AppBundle\Entity\Country

4. Additionally if you want to give the administrator an ability to add a ``flag`` to any of Countries,
you'll need to update its form type. Check how to do it :doc:`here </customization/form>`.

What happens while overriding Models?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

* Parameter ``sylius.model.country.class`` contains ``AppBundle\\Entity\\Country``.
* ``sylius.repository.country`` represents Doctrine repository for your new class.
* ``sylius.manager.country`` represents Doctrine object manager for your new class.
* ``sylius.controller.country`` represents the controller for your new class.
* All Doctrine relations to ``Sylius\\Adressing\\Model\\Country`` are using your new class as *target-entity*, you do not need to update any mappings.
* ``CountryType`` form type is using your model as ``data_class``.
* ``Sylius\\Addressing\\Model\\Country`` is automatically turned into Doctrine Mapped Superclass.
