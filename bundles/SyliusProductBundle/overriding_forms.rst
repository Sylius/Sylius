Overriding Forms
================

There are several ways to override the default forms and you have full freedom, you can even replace them completely.

Changing the form type class
----------------------------

To change the class behind ``sylius_product`` type or any other form from this bundle, you have to configure it in container.

Let's assume you want to add a "price" field and remove the default "availableOn" input to the form.

Firstly, you have to create new ``ProductType`` class.

.. code-block:: php

    <?php

    // src/Acme/ShopBundle/Form/Type/ProductType.php

    namespace Acme\ShopBundle\Form\Type;

    use Sylius\Bundle\ProductBundle\Form\Type\ProductType as BaseProductType;
    use Symfony\Component\Form\FormBuilderInterface;

    class ProductType extends BaseProductType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            parent::buildForm($builder, $options); // This will add the default fields.

            $builder
                ->remove('availableOn')
                ->add('price', 'money')
            ;
        }
    }

Secondly, you have to configure your new class in container.

.. code-block:: yaml

    # app/config/config.yml

    sylius_product:
        driver: doctrine/orm
        classes:
            product:
                form: Acme\ShopBundle\Form\Type\ProductType

That's it! The new form type class will be used. This is possible for all other forms, see the :doc:`configuration reference </bundles/SyliusProductBundle/configuration>`.

.. note::

    Please remember, that you also need to add new fields to the model. Read the :doc:`chapter about overriding models </bundles/SyliusProductBundle/overriding_models>`.

Using different form type
-------------------------

Thanks to flexibility of the controllers (see :doc:`SyliusResourceBundle </bundles/SyliusResourceBundle/index>`) you can use different form type per action (per route, actually).
Below you can see usage of custom form in product create action.

.. code-block:: yaml

    # routing.yml

    app_product_create:
        pattern: /products/new
        methods: [GET, POST]
        defaults:
            _controller: sylius.controller.product:updateAction
            _sylius:
                form: app_product

This action will use ``app_product`` form type instead of the default ``sylius_product``. If you wonder how to create this new form type, please check out the `Symfony documentation <http://symfony.com/doc/current/book/forms.html#creating-form-classes>`_.
