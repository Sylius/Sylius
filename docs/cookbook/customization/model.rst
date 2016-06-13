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
* Add ``description`` to the ``PaymentMethod``

And of course many similar operations limited only by your imagination.
Let's now see how you should perform such customizations.

How to customize a Model?
~~~~~~~~~~~~~~~~~~~~~~~~~

Let's take the ``Sylius\Component\Addressing\Country`` as an example. This one is not extended in Core.
Assuming that you would want to add another field on the model - for instance ``flag``.
To simplify let's say that it will be a string, where you will hold a path to the image file.

1. The first thing to do is to write your own class which will extend the base `Country`` class.

.. code-block:: php

    <?php

    namespace AppBundle\Entity;

    use Sylius\Component\Addressing\Model\Country as BaseCountry;

    /**
     * @author Name Surname <name.surname@test.com>
     */
    class Country extends BaseCountry
    {
        /**
         * @var string
         */
        private $flag;

        /**
         * @return string
         */
        public function getFlag()
        {
            return $this->flag;
        }

        /**
         * @param string $flag
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
            type: string
            nullable: true

3. Finally you'll need to override the model's class in the ``app/config/config.yml``.

.. code-block:: yaml

    sylius_addressing:
        resources:
            country:
                classes:
                    model: AppBundle\Entity\Country

4. Additionally if you want to give the administrator an ability to add a ``flag`` to any of Countries
you'll need to update its form type. Check how to do it :doc:`here </cookbook/customization/form>`.

What happens while overriding Models?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

* Parameter ``sylius.model.country.class`` contains ``AppBundle\\Entity\\Country``.
* ``sylius.repository.country`` represents Doctrine repository for your new class.
* ``sylius.manager.country`` represents Doctrine object manager for your new class.
* ``sylius.controller.country`` represents the controller for your new class.
* All Doctrine relations to ``Sylius\\Component\\Adressing\\Model\\Country`` are using your new class as *target-entity*, you do not need to update any mappings.
* ``CountryType`` form type is using your model as ``data_class``.
* ``Sylius\\Component\\Addressing\\Model\\Country`` is automatically turned into Doctrine Mapped Superclass.
