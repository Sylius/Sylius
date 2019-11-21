Customizing Forms
=================

The forms in Sylius are placed in the ``Sylius\Bundle\*BundleName*\Form\Type`` namespaces and the extensions
will be placed in `App\Form\Extension`.

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

.. tip::

    You can browse the full implementation of this example on `this GitHub Pull Request.
    <https://github.com/Sylius/Customizations/pull/8>`__

If you want to modify the form for the ``Customer Profile`` in your system there are a few steps that you should take.
Assuming that you would like to (for example):

* Add a ``secondaryPhoneNumber`` field,
* Remove the ``gender`` field,
* Change the label for the ``lastName`` from ``sylius.form.customer.last_name`` to ``app.form.customer.surname``

These will be the steps that you will have to take to achieve that:

**1.** If you are planning to add new fields remember that beforehand they need to be added on the model that the form type is based on.

    In case of our example if you need to have the ``secondaryPhoneNumber`` on the model and the entity mapping for the ``Customer`` resource.
    To get to know how to prepare that go :doc:`there </customization/model>`.

**2.** Create a **Form Extension**.

Your form has to extend a proper base class. How can you check that?

For the ``CustomerProfileType`` run:

.. code-block:: bash

    $ php bin/console debug:container sylius.form.type.customer_profile

As a result you will get the ``Sylius\Bundle\CustomerBundle\Form\Type\CustomerProfileType`` - this is the class that you need to be extending.

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Form\Extension;

    use Sylius\Bundle\CustomerBundle\Form\Type\CustomerProfileType;
    use Symfony\Component\Form\AbstractTypeExtension;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;

    final class CustomerProfileTypeExtension extends AbstractTypeExtension
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                // Adding new fields works just like in the parent form type.
                ->add('secondaryPhoneNumber', TextType::class, [
                    'required' => false,
                    'label' => 'app.form.customer.secondary_phone_number',
                ])
                // To remove a field from a form simply call ->remove(`fieldName`).
                ->remove('gender')
                // You can change the label by adding again the same field with a changed `label` parameter.
                ->add('lastName', TextType::class, [
                    'label' => 'app.form.customer.surname',
                ]);
        }

        public static function getExtendedTypes(): iterable
        {
            return [CustomerProfileType::class];
        }
    }

.. note::
    Of course remember that you need to define new labels for your fields
    in the ``translations\messages.en.yaml`` for english contents of your messages.

**3.** After creating your class, register this extension as a service in the ``config/services.yaml``:

.. code-block:: yaml

    services:
        app.form.extension.type.customer_profile:
            class: App\Form\Extension\CustomerProfileTypeExtension
            tags:
                - { name: form.type_extension, extended_type: Sylius\Bundle\CustomerBundle\Form\Type\CustomerProfileType }

.. note::
    Of course remember that you need to render the new fields you have created,
    and remove the rendering of the fields that you have removed **in your views**.

In our case you will need to copy the original template from ``vendor/sylius/sylius/src/Sylius/Bundle/ShopBundle/Resources/views/Account/profileUpdate.html.twig``
to ``templates/bundles/SyliusShopBundle/Account/`` and add the fields inside the copy.

.. code-block:: html

    {{ form_row(form.phoneNumber) }}
    {{ form_row(form.subscribedToNewsletter) }}

    <!-- your fields -->
    {{ form_row(form.birthday) }}
    {{ form_row(form.secondaryPhoneNumber) }}

    {{ sonata_block_render_event('sylius.shop.account.profile.update.form', {'customer': customer, 'form': form}) }}

Need more information?
----------------------

.. warning::

    Some of the forms already have extensions in Sylius. Learn more about Extensions `here <http://symfony.com/doc/current/form/create_form_type_extension.html>`_.

For instance the ``ProductVariant`` admin form is defined under ``Sylius/Bundle/ProductBundle/Form/Type/ProductVariantType.php`` and later extended in
``Sylius/Bundle/CoreBundle/Form/Extension/ProductVariantTypeExtension.php``. If you again extend the base type form like this:

.. code-block:: yaml

    services:
        app.form.extension.type.product_variant:
            class: App\Form\Extension\ProductVariantTypeMyExtension
            tags:
                - { name: form.type_extension, extended_type: Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType, priority: -5 }

your form extension will also be executed. Whether before or after the other extensions depends on priority tag set.

`How to customize forms that are already extended in Core?`
-----------------------------------------------------------

.. tip::

    You can browse the full implementation of this example on `this GitHub Pull Request.
    <https://github.com/Sylius/Customizations/pull/9>`__

Having a look at the extensions and possible additionally defined event handlers can also be useful when form elements are embedded dynamically,
as is done in the ``ProductVariantTypeExtension`` by the ``CoreBundle``:

.. code-block:: php

    <?php

    ...

    final class ProductVariantTypeExtension extends AbstractTypeExtension
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            ...

            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $productVariant = $event->getData();

                $event->getForm()->add('channelPricings', ChannelCollectionType::class, [
                    'entry_type' => ChannelPricingType::class,
                    'entry_options' => function (ChannelInterface $channel) use ($productVariant) {
                        return [
                            'channel' => $channel,
                            'product_variant' => $productVariant,
                            'required' => false,
                        ];
                    },
                    'label' => 'sylius.form.variant.price',
                ]);
            });
        }

        ...

    }

The ``channelPricings`` get added on ``FormEvents::PRE_SET_DATA``, so when you wish to remove or alter this form definition,
you will also have to set up an event listener and then remove the field:

.. code-block:: php

    <?php

    ...

    final class ProductVariantTypeMyExtension extends AbstractTypeExtension
    {
        ...

        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            ...

            $builder
                ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                    $event->getForm()->remove('channelPricings');
                })
                ->addEventSubscriber(new AddCodeFormSubscriber(NULL, ['label' => 'app.form.my_other_code_label']))
            ;

            ...

        }
    }

Adding constraints inside a form extension
------------------------------------------

.. warning::

    When adding your constraints dynamically from inside a form extension, be aware to add the correct validation groups.

Although it is advised to follow the :doc:`Validation Customization Guide </customization/validation>`, it might happen that you
want to define the form constraints from inside the form extension. They will not be used unless the correct validation group(s)
has been added. The example below shows how to add the default `sylius` group to a constraint.

.. tip::

    You can browse the full implementation of this example on `this GitHub Pull Request.
    <https://github.com/Sylius/Customizations/pull/8>`__

.. code-block:: php

    <?php

    ...

    final class CustomerProfileTypeExtension extends AbstractTypeExtension
    {
        ...

        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            ...

            // Adding new fields works just like in the parent form type.
            ->add('secondaryPhoneNumber', TextType::class, [
                'required' => false,
                'label' => 'app.form.customer.secondary_phone_number',
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'max' => 10,
                        'groups' => ['sylius'],
                    ]),
                ],
            ]);

            ...

        }

        ...

    }

Overriding forms completely
---------------------------

.. tip::

    If you need to create a new form type on top of an existing one -  create this new alternative form type and define `getParent()`
    to the old one. `See details in the Symfony docs <http://symfony.com/doc/current/form/create_custom_field_type.html>`_.

.. include:: /customization/plugins.rst.inc
