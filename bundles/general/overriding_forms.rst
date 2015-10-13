Overriding Forms
================

Every form type in Sylius holds its class name in a specific parameter. This allows you to easily add or remove fields by extending the base form class.

Extending base Forms
--------------------

All Sylius form types live in ``Sylius\Bundle\XyzBundle\Form\Type`` namespace.

Let's assume you want to add "phone" and remove "company" fields to the Sylius address form.

You have to create your own ``AddressType`` class, which will extend the base form type.

.. code-block:: php

    namespace Acme\Bundle\ShopBundle\Form\Type;

    use Sylius\Bundle\AddressingBundle\Form\Type\AddressType as BaseAddressType;
    use Symfony\Component\Form\FormBuilderInterface;

    class AddressType extends BaseAddressType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            parent::buildForm($builder, $options); // Add default fields.

            $builder->remove('company');
            $builder->add('phone', 'text', array('required' => false));
        }
    }

Now, define the new form class in the ``app/config/config.yml``.

.. code-block:: yaml

    # app/config/config.yml

    sylius_addressing:
        driver: doctrine/orm
        classes:
            address:
                form: 
                    default: Acme\ShopBundle\Form\Type\AddressType

Done! Sylius will now use your custom address form everywhere!

What has happened?

* Parameter ``sylius.form.type.address.class`` contains ``Acme\\Bundle\\ShopBundle\\Form\\Type\\AddressType``.
* ``sylius.form.type.address`` form type service uses your custom class.
* ``sylius_address`` form type uses your new form everywhere.
