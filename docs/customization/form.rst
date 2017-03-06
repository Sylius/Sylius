Customizing Forms
=================

The forms in Sylius are placed in the ``Sylius\Bundle\*BundleName*\Form\Type`` namespaces and the extensions
will be placed in `AppBundle\Form\Extension`.

Why would you customize a Form?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

There are plenty of reasons to modify forms that have already been defined in Sylius.
Your business needs may sometimes slightly differ from our internal assumptions.

You can:

* add completely **new fields**,
* **modify** existing fields, make them required, change their HTML class, change labels etc.,
* **remove** fields that are not used.

How to customize a Form?
~~~~~~~~~~~~~~~~~~~~~~~~

If you want to modify the form for the ``Customer Profile`` in your system there are a few steps that you should take.
Assuming that you would like to (for example):

* Add a ``contactHours`` field,
* Remove the ``gender`` field,
* Change the label for the ``lastName`` from ``sylius.form.customer.last_name`` to ``app.form.customer.surname``

These will be the steps that you will have to take to achieve that:

**1.** If your are planning to add new fields remember that beforehand they need to be added on the model that the form type is based on.

    In case of our example if you need to have the ``contactHours`` on the model and the entity mapping for the ``Customer`` resource.
    To get to know how to prepare that go :doc:`there </customization/model>`.

**2.** Create a **Form Extension**.

Your form has to extend a proper base class. How can you check that?

For the ``CustomerProfileType`` run:

.. code-block:: bash

    $ php bin/console debug:container sylius.form.type.customer_profile

As a result you will get the ``Sylius\Bundle\CustomerBundle\Form\Type\CustomerProfileType`` - this is the class that you need to be extending.

.. code-block:: php

    <?php

    namespace AppBundle\Form\Extension;

    use Sylius\Bundle\CustomerBundle\Form\Type\CustomerProfileType;
    use Symfony\Component\Form\AbstractTypeExtension;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;

    final class CustomerProfileTypeExtension extends AbstractTypeExtension
    {
        /**
         * {@inheritdoc}
         */
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            // Adding new fields works just like in the parent form type.
            $builder->add('contactHours', TextType::class, [
                'required' => false,
                'label' => 'app.form.customer.contact_hours',
            ]);

            // To remove a field from a form simply call ->remove(`fieldName`).
            $builder->remove('gender');

            // You can change the label by adding again the same field with a changed `label` parameter.
            $builder->add('lastName', TextType::class, [
                'label' => 'app.form.customer.surname',
            ]);
        }

        /**
         * {@inheritdoc}
         */
        public function getExtendedType()
        {
            return CustomerProfileType::class;
        }
    }

.. note::
    Of course remember that you need to define new labels for your fields
    in the ``app\Resources\translations\messages.en.yml`` for english contents of your messages.

**3.** After creating your class, register this extension as a service in the ``AppBundle/Resources/config/services.yml``:

.. code-block:: yaml

    services:
        app.form.extension.type.customer_profile:
            class: AppBundle\Form\Extension\CustomerProfileTypeExtension
            tags:
                - { name: form.type_extension, extended_type: Sylius\Bundle\CustomerBundle\Form\Type\CustomerProfileType }

.. note::
    Of course remember that you need to render the new fields you have created,
    and remove the rendering of the fields that you have removed **in your views**.

In our case you will need a new template: `app/Resources/SyliusShopBundle/views/Account/profileUpdate.html.twig`.

In **Twig** for example you can render your modified form in such a way:

.. code-block:: html

    <div class="two fields">
        <div class="field">{{ form_row(form.birthday) }}</div>
        <div class="field">{{ form_row(form.contactHours) }}</div>
    </div>

Need more information?
----------------------

.. warning::

    Some of the forms already have extensions in Sylius. Learn more about Extensions `here <http://symfony.com/doc/current/bundles/extension.html>`_.

Overriding forms completely
---------------------------

.. tip::

    If you need to create a new form type on top of an existing one -  create this new alternative form type and define `getParent()`
    to the old one. `See details in the Symfony docs <http://symfony.com/doc/current/form/create_custom_field_type.html>`_.
