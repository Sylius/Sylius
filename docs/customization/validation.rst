Customizing Validation
======================

The default validation group for all resources is ``sylius``, but you can configure your own validation.

How to customize validation?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. tip::

    You can browse the full implementation of these examples on `this GitHub Pull Request.
    <https://github.com/Sylius/Customizations/pull/15>`_

Let's take the example of changing the length of ``name`` for the ``Product`` entity - watch out the field ``name`` is hold on the ``ProductTranslation`` model.

In the ``sylius`` validation group the minimum length is equal to 2.
What if you'd want to have at least 10 characters?

**1.** Create the ``config/validator/validation.yaml``.

In this file you need to overwrite the whole validation of your field that you are willing to modify.
Take this configuration from the ``src/Sylius/Bundle/ProductBundle/Resources/config/validation/ProductTranslation.xml`` - you can choose format ``xml`` or ``yaml``.

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

    When using custom validation messages see `here how to add them <https://symfony.com/doc/current/validation/translations.html>`_.

**2.** Configure the new validation group in the ``config/services.yaml``.

.. code-block:: yaml

    # config/services.yaml
    parameters:
        sylius.form.type.product_translation.validation_groups: [app_product]
        sylius.form.type.product.validation_groups: [app_product] # the product class also needs to be aware of the translation's validation

Done. Now in all forms where the Product ``name`` is being used, your new validation group will be applied,
not letting users add products with name shorter than 10 characters.

.. tip::

    When you would like to use group sequence validation, `like so <https://symfony.com/doc/current/validation/sequence_provider.html>`_.
    Be sure to use ``[Default]`` as validation group. Otherwise your ``getGroupSequence()`` method will not be called.

.. include:: /customization/plugins.rst
