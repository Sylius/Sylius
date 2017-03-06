Customizing Validation
======================

The default validation group for all resources is ``sylius``, but you can configure your own validation.

How to customize validation?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Let's take the example of changing the length of ``name`` for the ``Product`` entity - watch out the field ``name`` is hold on the ``ProductTranslation`` model.

In the ``sylius`` validation group the minimum length is equal to 2.
What if you'd want to have at least 10 characters?

**1.** Create the ``AppBundle/Resources/config/validation.yml``.

In this file you need to overwrite the whole validation of your field that you are willing to modify.
Take this configuration from the ``Sylius/Bundle/ProductBundle/Resources/config/validation/ProductTranslation.xml`` - you can choose format ``xml`` or ``yaml``.

Give it a new, custom validation group - ``[app_product]``.

.. code-block:: yaml

    Sylius\Component\Product\Model\ProductTranslation:
        properties:
            name:
                - NotBlank:
                    message: sylius.product.name.not_blank
                    groups: [app_product]
                - Length:
                    min: 10
                    minMessage: sylius.product.name.min_length
                    max: 255
                    maxMessage: sylius.product.name.max_length
                    groups: [app_product]

.. tip::

    When using custom validation messages see `here how to add them <http://symfony.com/doc/current/validation/translations.html>`_.

**2.** Configure the new validation group in the ``app/config/services.yml``.

.. code-block:: yaml

    # app/config/services.yml
    parameters:
        sylius.form.type.product_translation.validation_groups: [app_product]
        sylius.form.type.product.validation_groups: [app_product] # the product class also needs to be aware of the translation'a validation

Remember to import the ``app/config/services.yml`` into the ``app/config/config.yml``.

.. code-block:: yaml

    # app/config/config.yml
    imports:
        - { resource: "services.yml" }

Done. Now in all forms where the Product ``name`` is being used, your new validation group will be applied,
not letting users add products with name shorter than 10 characters.
