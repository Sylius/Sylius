How to add images to an entity?
===============================

Extending entities with an `images` field is quite a popular usecase.
In this cookbook we will present how to **add image to the Shipping Method entity**.

Instructions:
-------------

1. Extend the ShippingMethodInterface with the ImageAwareInterface
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

2. Implement the new ShippingMethodInterface in your own ShippingMethod class
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

3. Modify the ShippingMethod's mapping file
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

4. Register your extended ShippingMethod as a resource
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

5. Create the ShippingMethodImage class
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

6. Add the mapping file for the ShippingMethodImage
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Your new entity will be saved in the databes, therefore it needs a mapping file, where you will set the ``ShippingMethod`` as the ``owner``
of the ``ShippingMethodImage``.

.. code-block:: yaml

    # AppBundle/Resources/config/doctrine/ShippingMethodImage.orm.yml
    AppBundle\Entity\ShippingMethodImage:
        type: entity
        table: app_shipping_method_image
        manyToOne:
            owner:
                targetEntity: AppBundle\Entity\ShippingMethod
                inversedBy: images
                joinColumn:
                    name: owner_id
                    referencedColumnName: id
                    nullable: false
                    onDelete: CASCADE

7. Register the ShippingMethodImage as a resource
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The ShippingMethodImage class needs to be registered as a Sylius resource:

.. code-block:: yaml

    # app/config/config.yml
    sylius_resource:
        resources:
            app.shipping_method_image:
                classes:
                    model: AppBundle\Entity\ShippingMethodImage

6. Create the ShippingMethodImageType class
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

This is how the class for ShippingMethodImageType should look like. Place it in the ``AppBundle\Form\Type\ShippingMethod`` directory.

.. code-block:: php

    <?php

    namespace AppBundle\Form\Type\ShippingMethod;

    use Sylius\Bundle\CoreBundle\Form\Type\ImageType;

    final class ShippingMethodImageType extends ImageType
    {
        /**
         * {@inheritdoc}
         */
        public function getBlockPrefix()
        {
            return 'app_shipping_method_image';
        }
    }

7. Register the ShippingMethodImageType as a service
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

After creating the form type class, you need to register it as a ``form.type`` service liek below:

.. code-block:: yaml

    # services.yml
    services:
        app.form.type.shipping_method_image:
            class: AppBundle\Form\Type\ShippingMethod\ShippingMethodImageType
            tags:
                - { name: form.type }
            arguments: ['%app.model.shipping_method_image.class%']

8. Add the ShippingMethodImageType to the resource form configuration
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

What is more the new form type needs to be configured as the resource form of the ``ShippingMethodImage``:

.. code-block:: yaml

    # app/config/config.yml
    sylius_resource:
        resources:
            app.shipping_method_image:
                classes:
                    form: AppBundle\Form\Type\ShippingMethodImageType

9. Extend the ShippingMethodType with the images field
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. tip::

    Read more about :doc:`customizing forms via extensions in the dedicated guide </customization/form>`.

**Create the form extension class** for the ``Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType``:

It needs to have the images field as a CollectionType. If you want to give a possibility to add more than one image to the entity
set the ``allow_add`` option to ``true``.

.. code-block:: php

    <?php

    namespace AppBundle\Form\Extension;

    use AppBundle\Form\Type\ShippingMethod\ShippingMethodImageType;
    use Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType;
    use Symfony\Component\Form\AbstractTypeExtension;
    use Symfony\Component\Form\Extension\Core\Type\CollectionType;
    use Symfony\Component\Form\FormBuilderInterface;

    final class ShippingMethodTypeExtension extends AbstractTypeExtension
    {
        /**
         * {@inheritdoc}
         */
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder->add('images', CollectionType::class, [
                'entry_type' => ShippingMethodImageType::class,
                'allow_add' => false,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'sylius.form.shipping_method.images',
            ]);
        }

        /**
         * {@inheritdoc}
         */
        public function getExtendedType()
        {
            return ShippingMethodType::class;
        }
    }

Register the form extension as a service:

.. code-block:: yaml

    # services.yml
    services:
        app.form.extension.type.shipping_method:
            class: AppBundle\Form\Extension\ShippingMethodTypeExtension
            tags:
                - { name: form.type_extension, extended_type: Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType }

10. Override the definition of the ImageUploader service
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

In order to handle the image upload you need to attach the image upload listener to the ShippingMethod entity events:

.. code-block:: yaml

    # services.yml
    services:
        sylius.listener.image_upload:
            class: Sylius\Bundle\CoreBundle\EventListener\ImageUploadListener
            arguments: ['@sylius.image_uploader']
            tags:
                - { name: kernel.event_listener, event: "sylius.product.pre_create", method: "uploadImage" }
                - { name: kernel.event_listener, event: "sylius.product.pre_update", method: "uploadImage" }
                - { name: kernel.event_listener, event: "sylius.taxon.pre_create", method: "uploadImage" }
                - { name: kernel.event_listener, event: "sylius.taxon.pre_update", method: "uploadImage" }
                - { name: kernel.event_listener, event: "sylius.shipping_method.pre_create", method: "uploadImage" }
                - { name: kernel.event_listener, event: "sylius.shipping_method.pre_update", method: "uploadImage" }

11. Render the images field in the form view
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

In order to achieve that you will need to customize the form view from the ``SyliusAdminBundle/views/ShippingMethod/_form.html.twig`` file.

Copy and pase its contents into your own ``app/Resources/SyliusAdminBundle/views/ShippingMethod/_form.html.twig`` file,
and render the ``{{ form_row(form.images) }}`` field.

.. code-block:: twig

    {# app/Resources/SyliusAdminBundle/views/ShippingMethod/_form.html.twig #}

    {% from '@SyliusAdmin/Macro/translationForm.html.twig' import translationForm %}

    <div class="ui two column stackable grid">
        <div class="column">
            <div class="ui segment">
                {{ form_errors(form) }}
                <div class="three fields">
                    {{ form_row(form.code) }}
                    {{ form_row(form.zone) }}
                    {{ form_row(form.position) }}
                </div>
                {{ form_row(form.enabled) }}
                <h4 class="ui dividing header">{{ 'sylius.ui.availability'|trans }}</h4>
                {{ form_row(form.channels) }}
                <h4 class="ui dividing header">{{ 'sylius.ui.category_requirements'|trans }}</h4>
                {{ form_row(form.category) }}
                {% for categoryRequirementChoiceForm in form.categoryRequirement %}
                    {{ form_row(categoryRequirementChoiceForm) }}
                {% endfor %}
                <h4 class="ui dividing header">{{ 'sylius.ui.taxes'|trans }}</h4>
                {{ form_row(form.taxCategory) }}
                <h4 class="ui dividing header">{{ 'sylius.ui.shipping_charges'|trans }}</h4>
                {{ form_row(form.calculator) }}
                {% for name, calculatorConfigurationPrototype in form.vars.prototypes %}
                    <div id="{{ form.calculator.vars.id }}_{{ name }}" data-container=".configuration"
                         data-prototype="{{ form_widget(calculatorConfigurationPrototype)|e }}">
                    </div>
                {% endfor %}

                {# Here you go! #}
                {{ form_row(form.images) }}

                <div class="ui segment configuration">
                    {% if form.configuration is defined %}
                        {% for field in form.configuration %}
                            {{ form_row(field) }}
                        {% endfor %}
                    {% endif %}
                </div>
            </div>
        </div>
        <div class="column">
            {{ translationForm(form.translations) }}
        </div>
    </div>

.. tip::

    Learn more about customizing templates :doc:`here </customization/template>`.

Learn more
----------

* :doc:`GridBundle documentation </bundles/SyliusGridBundle/index>`
* :doc:`ResourceBundle documentation </bundles/SyliusResourceBundle/index>`
* :doc:`Customization Guide </customization/index>`
