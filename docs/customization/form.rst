Customizing Forms
=================

The forms in Sylius are placed in the ``Sylius\Bundle\*BundleName*\Form\Type`` namespaces.

.. warning::
    Many forms in Sylius are **extended in the CoreBundle**.
    If the form you are willing to override exists in the ``CoreBundle`` your should be extending the one from Core, not the base form from the bundle.

Why would you customize a Form?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

There are plenty of reasons to modify forms that have already been defined in Sylius.
Your business needs may sometimes slightly differ from our internal assumptions.

You can:

* add completely **new fields**, if you need another phone number for your customers,
* **modify** existing fields, make them required, change their HTML class, change labels etc.,
* **remove** fields that are not used.

How to customize a Form?
~~~~~~~~~~~~~~~~~~~~~~~~

If you want to modify the form for the ``Address`` in your system there are a few steps that you should take.
Assuming that you would like to (for example):

* Add a ``contactHours`` field,
* Remove the ``company`` field,
* Change the label for the ``lastName`` from ``sylius.form.address.last_name`` to ``app.form.address.surname``

These will be the steps that you will have to take to achieve that:

1. If your are planning to add new fields remember that beforehand they need to be added on the model that the form type is based on.

    In case of our example if you need to have the ``contactHours`` on the model and the entity mapping for the ``Address`` resource.
    To get to know how to prepare that go :doc:`there </customization/model>`.

2. Write your own form type class that will be extending the default one. Place it in your ``AppBundle\Form\Type`` directory.

Your new class has to extend a proper base class. How can you check that?

For the ``AddressType`` run:

.. code-block:: bash

    $ php app/console debug:container sylius.form.type.address

As a result you will get the ``Sylius\Bundle\AddressingBundle\Form\Type\AddressType`` - this is the class that you need to be extending.

.. code-block:: php

    <?php

    namespace AppBundle\Form\Type;

    use Sylius\Bundle\AddressingBundle\Form\Type\AddressType as BaseAddressType;
    use Symfony\Component\Form\FormBuilderInterface;

    class AddressType extends BaseAddressType
    {
        /**
         * {@inheritdoc}
         */
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            // Add default fields from the `BaseAddressType` that you are extending.
            parent::buildForm($builder, $options);

            // Adding new fields works just like in the parent form.
            $builder->add('contactHours', 'text', [
                'required' => false,
                'label' => 'app.form.address.contact_hours',
            ]);

            // To remove a field from a form simply call ->remove(`fieldName`).
            $builder->remove('company');

            // You can change the label by adding again the same field with a changed `label` parameter.
            $builder->add('lastName', 'text', [
                'label' => 'app.form.address.surname',
            ]);
        }
    }

3. Define your newly created class in the ``app/config/config.yml``.

.. code-block:: yaml

    sylius_addressing:
        resources:
            address:
                classes:
                    form:
                        default: AppBundle\Form\Type\AddressType

.. note::
    Of course remember that you need to render the new fields you have created,
    and remove the rendering of the fields that you have removed **in your views**.

In **Twig** for example you can render your modified form in such a way:

.. code-block:: html

    <div id="addressForm">
        {{ form_row(form.firstName) }}
        {{ form_row(form.lastName) }}
        {{ form_row(form.city) }}
        {{ form_row(form.street) }}
        {{ form_row(form.postcode) }}
        {{ form_row(form.countryCode) }}
        {{ form_row(form.provinceCode) }}
        {{ form_row(form.phoneNumber) }}
        {{ form_row(form.contactHours) }}
    </div>

What happens while overriding Forms?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

* Parameter ``sylius.form.type.address.class`` contains the ``AppBundle\Form\Type\AddressType``.
* ``sylius.form.type.address`` form type service uses your custom class.
* ``sylius_address`` form type uses your new form everywhere.
