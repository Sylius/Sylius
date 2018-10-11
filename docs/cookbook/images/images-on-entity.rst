How to add images to an entity?
===============================

Extending entities with an ``images`` field is quite a popular use case.
In this cookbook we will present how to **add image to the Shipping Method entity**.

Instructions:
-------------

1. Extend the ShippingMethod class with the ImagesAwareInterface
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

In order to override the ``ShippingMethod`` that lives inside of the SyliusCoreBundle,
you have to create your own ShippingMethod class that will extend it:

.. code-block:: php

    <?php
    
    declare(strict_types=1);

    namespace App\Entity;

    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\Common\Collections\Collection;
    use Sylius\Component\Core\Model\ImagesAwareInterface;
    use Sylius\Component\Core\Model\ImageInterface;
    use Sylius\Component\Core\Model\ShippingMethod as BaseShippingMethod;

    class ShippingMethod extends BaseShippingMethod implements ImagesAwareInterface
    {
        /**
         * @var Collection|ImageInterface[]
         */
        protected $images;

        public function __construct()
        {
            parent::__construct();

            $this->images = new ArrayCollection();
        }

        /**
         * {@inheritdoc}
         */
        public function getImages(): Collection
        {
            return $this->images;
        }

        /**
         * {@inheritdoc}
         */
        public function getImagesByType(string $type): Collection
        {
            return $this->images->filter(function (ImageInterface $image) use ($type) {
                return $type === $image->getType();
            });
        }

        /**
         * {@inheritdoc}
         */
        public function hasImages(): bool
        {
            return !$this->images->isEmpty();
        }

        /**
         * {@inheritdoc}
         */
        public function hasImage(ImageInterface $image): bool
        {
            return $this->images->contains($image);
        }

        /**
         * {@inheritdoc}
         */
        public function addImage(ImageInterface $image): void
        {
            $image->setOwner($this);
            $this->images->add($image);
        }

        /**
         * {@inheritdoc}
         */
        public function removeImage(ImageInterface $image): void
        {
            if ($this->hasImage($image)) {
                $image->setOwner(null);
                $this->images->removeElement($image);
            }
        }
    }

.. tip::

    Read more about customizing models in the docs :doc:`here </customization/model>`.

2. Register your extended ShippingMethod as a resource's model class
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

With such a configuration in the ``config.yml`` you will register your ShippingMethod class in order to override the default one:

.. code-block:: yaml

    # app/config/config.yml
    sylius_shipping:
        resources:
            shipping_method:
                classes:
                    model: App\Entity\ShippingMethod

3. Create the ShippingMethodImage class
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

In the ``App\Entity`` namespace place the ``ShippingMethodImage`` class which should look like this:

.. code-block:: php

    <?php
    
    declare(strict_types=1);

    namespace App\Entity;

    use Sylius\Component\Core\Model\Image;

    class ShippingMethodImage extends Image
    {
    }

4. Add the mapping file for the ShippingMethodImage
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Your new entity will be saved in the database, therefore it needs a mapping file, where you will set the ``ShippingMethod`` as the ``owner``
of the ``ShippingMethodImage``.

.. code-block:: yaml

    # AppBundle/Resources/config/doctrine/ShippingMethodImage.orm.yml
    App\Entity\ShippingMethodImage:
        type: entity
        table: app_shipping_method_image
        manyToOne:
            owner:
                targetEntity: App\Entity\ShippingMethod
                inversedBy: images
                joinColumn:
                    name: owner_id
                    referencedColumnName: id
                    nullable: false
                    onDelete: CASCADE

5. Modify the ShippingMethod's mapping file
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The newly added ``images`` field has to be added to the mapping, with a relation to the ``ShippingMethodImage``:

.. code-block:: yaml

    # AppBundle/Resources/config/doctrine/ShippingMethod.orm.yml
    App\Entity\ShippingMethod:
        type: entity
        table: sylius_shipping_method
        oneToMany:
            images:
                targetEntity: App\Entity\ShippingMethodImage
                mappedBy: owner
                orphanRemoval: true
                cascade:
                    - all

6. Register the ShippingMethodImage as a resource
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The ``ShippingMethodImage`` class needs to be registered as a Sylius resource:

.. code-block:: yaml

    # app/config/config.yml
    sylius_resource:
        resources:
            app.shipping_method_image:
                classes:
                    model: App\Entity\ShippingMethodImage

7. Create the ShippingMethodImageType class
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

This is how the class for ``ShippingMethodImageType`` should look like. Place it in the ``App\Form\Type\`` directory.

.. code-block:: php

    <?php
    
    declare(strict_types=1);

    namespace App\Form\Type;

    use Sylius\Bundle\CoreBundle\Form\Type\ImageType;

    final class ShippingMethodImageType extends ImageType
    {
        /**
         * {@inheritdoc}
         */
        public function getBlockPrefix(): string
        {
            return 'app_shipping_method_image';
        }
    }

8. Register the ShippingMethodImageType as a service
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

After creating the form type class, you need to register it as a ``form.type`` service like below:

.. code-block:: yaml

    # services.yml
    services:
        app.form.type.shipping_method_image:
            class: App\Form\Type\ShippingMethodImageType
            tags:
                - { name: form.type }
            arguments: ['%app.model.shipping_method_image.class%']

9. Add the ShippingMethodImageType to the resource form configuration
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

What is more the new form type needs to be configured as the resource form of the ``ShippingMethodImage``:

.. code-block:: yaml

    # app/config/config.yml
    sylius_resource:
        resources:
            app.shipping_method_image:
                classes:
                    form: App\Form\Type\ShippingMethodImageType

10. Extend the ShippingMethodType with the images field
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. tip::

    Read more about :doc:`customizing forms via extensions in the dedicated guide </customization/form>`.

**Create the form extension class** for the ``Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType``:

It needs to have the images field as a CollectionType.

.. code-block:: php

    <?php
    
    declare(strict_types=1);

    namespace App\Form\Extension;

    use App\Form\Type\ShippingMethodImageType;
    use Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType;
    use Symfony\Component\Form\AbstractTypeExtension;
    use Symfony\Component\Form\Extension\Core\Type\CollectionType;
    use Symfony\Component\Form\FormBuilderInterface;

    final class ShippingMethodTypeExtension extends AbstractTypeExtension
    {
        /**
         * {@inheritdoc}
         */
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder->add('images', CollectionType::class, [
                'entry_type' => ShippingMethodImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'sylius.form.shipping_method.images',
            ]);
        }

        /**
         * {@inheritdoc}
         */
        public function getExtendedType(): string
        {
            return ShippingMethodType::class;
        }
    }

.. tip::

    In case you need only a single image upload, this can be done in 2 very easy steps.
    
    First, in the code for the form provided above set ``allow_add`` and ``allow_delete`` to ``false``
    
    Second, in the ``__construct`` method of the ``ShippingMethod`` entity you defined earlier add the following:
    
    .. code-block:: php
    
        public function __construct()
        {
            parent::__construct();
            $this->images = new ArrayCollection();
            $this->addImage(new ShippingMethodImage());
        }

Register the form extension as a service:

.. code-block:: yaml

    # services.yml
    services:
        app.form.extension.type.shipping_method:
            class: App\Form\Extension\ShippingMethodTypeExtension
            tags:
                - { name: form.type_extension, extended_type: Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType }

11. Declare the ImagesUploadListener service
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

In order to handle the image upload you need to attach the ``ImagesUploadListener`` to the ``ShippingMethod`` entity events:

.. code-block:: yaml

    # services.yml
    services:
        app.listener.images_upload:
            class: Sylius\Bundle\CoreBundle\EventListener\ImagesUploadListener
            parent: sylius.listener.images_upload
            autowire: true
            autoconfigure: false
            public: false
            tags:
                - { name: kernel.event_listener, event: sylius.shipping_method.pre_create, method: uploadImages }
                - { name: kernel.event_listener, event: sylius.shipping_method.pre_update, method: uploadImages }

12. Render the images field in the form view
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

In order to achieve that you will need to customize the form view from the ``SyliusAdminBundle/views/ShippingMethod/_form.html.twig`` file.

Copy and paste its contents into your own ``app/Resources/SyliusAdminBundle/views/ShippingMethod/_form.html.twig`` file,
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

13. Validation
^^^^^^^^^^^^^^

Your form so far is working fine, but don't forget about validation.
The easiest way is using validation config files under the ``AppBundle/Resources/config/validation`` folder.

This could look like this e.g.:

.. code-block:: yaml

    # AppBundle\Resources\config\validation\ShippingMethodImage.yml
    App\Entity\ShippingMethodImage:
      properties:
        file:
          - Image:
              groups: [sylius]
              maxHeight: 1000
              maxSize: 10240000
              maxWidth: 1000
              mimeTypes: 
                - "image/png"
                - "image/jpg"
                - "image/jpeg"
                - "image/gif"
              mimeTypesMessage: 'This file format is not allowed. Please use PNG, JPG or GIF files.'
              minHeight: 200
              minWidth: 200
              
This defines the validation constraints for each image entity.
Now connecting the validation of the ``ShippingMethod`` to the validation of each single ``Image Entity`` is left:

.. code-block:: yaml

    # AppBundle\Resources\config\validation\ShippingMethod.yml
    App\Entity\ShippingMethod:
      properties:
        ...
        images:
          - Valid: ~    

Learn more
----------

* :doc:`GridBundle documentation </components_and_bundles/bundles/SyliusGridBundle/index>`
* :doc:`ResourceBundle documentation </components_and_bundles/bundles/SyliusResourceBundle/index>`
* :doc:`Customization Guide </customization/index>`
