How to add a custom translatable model?
=======================================

In this guide we will create a new translatable model in our system, which is quite similar to :doc:`adding a simple model </cookbook/entities/custom-model>`,
although it requires some additional steps.

As an example we will take a **translatable Supplier entity**, which may be really useful for shop maintenance.

1. Define your needs
--------------------

A Supplier needs three essential fields: ``name``, ``description`` and ``enabled`` flag.
The **name and description** fields need to be translatable.

2. Generate the SupplierTranslation entity
------------------------------------------

Symfony, the framework Sylius uses, provides the `SensioGeneratorBundle <http://symfony.com/doc/current/bundles/SensioGeneratorBundle/index.html>`_,
that simplifies the process of adding a model.

.. warning::

    Remember to have the ``SensioGeneratorBundle`` imported in the AppKernel, as it is not there by default.

You need to use such a command in your project directory.

.. code-block:: bash

    $ php bin/console generate:doctrine:entity

The generator will ask you for the entity name and fields. See how it should look like to match our assumptions.

.. image:: ../../_images/generating_translation_entity.png
    :align: center

As you can see we have provided only the desired translatable fields.

Below the final ``SupplierTranslation`` class is presented, it implements the ``ResourceInterface``.

.. code-block:: php

    <?php

    namespace App\Entity;

    use Sylius\Component\Resource\Model\AbstractTranslation;
    use Sylius\Component\Resource\Model\ResourceInterface;

    class SupplierTranslation extends AbstractTranslation implements ResourceInterface
    {
        /**
         * @var int
         */
        private $id;

        /**
         * @var string
         */
        private $name;

        /**
         * @var string
         */
        private $description;

        /**
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * @param string $name
         */
        public function setName($name)
        {
            $this->name = $name;
        }

        /**
         * @return string
         */
        public function getName()
        {
            return $this->name;
        }

        /**
         * @param string $description
         */
        public function setDescription($description)
        {
            $this->description = $description;
        }

        /**
         * @return string
         */
        public function getDescription()
        {
            return $this->description;
        }
    }

3. Generate the Supplier entity
-------------------------------

While generating the entity, similarly to the way the translation was generated, we are providing only non-translatable fields.
In our case only the ``enabled`` field.

.. image:: ../../_images/generating_basic_entity.png
    :align: center

Having the stubs generated, we need to extend our class with a connection to SupplierTranslation.

* implement the ``ResourceInterface``,
* implement the ``TranslatableInterface``,
* use the ``TranslatableTrait``,
* initialize the translations collection in the constructor,
* add the ``createTranslation()`` method,
* implement getters and setters for the properties that are held on the translation model.

As a result you should get such a ``Supplier`` class:

.. code-block:: php

    <?php

    namespace App\Entity;

    use Sylius\Component\Resource\Model\ResourceInterface;
    use Sylius\Component\Resource\Model\TranslatableInterface;
    use Sylius\Component\Resource\Model\TranslatableTrait;

    class Supplier implements ResourceInterface, TranslatableInterface
    {
        use TranslatableTrait {
            __construct as private initializeTranslationsCollection;
        }

        public function __construct()
        {
            $this->initializeTranslationsCollection();
        }

        /**
         * @var int
         */
        private $id;

        /**
         * @var bool
         */
        private $enabled;

        /**
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * @param string $name
         */
        public function setName($name)
        {
            $this->getTranslation()->setName($name);
        }

        /**
         * @return string
         */
        public function getName()
        {
            return $this->getTranslation()->getName();
        }

        /**
         * @param string $description
         */
        public function setDescription($description)
        {
            $this->getTranslation()->setDescription($description);
        }

        /**
         * @return string
         */
        public function getDescription()
        {
            return $this->getTranslation()->getDescription();
        }

        /**
         * @param boolean $enabled
         */
        public function setEnabled($enabled)
        {
            $this->enabled = $enabled;
        }

        /**
         * @return bool
         */
        public function getEnabled()
        {
            return $this->enabled;
        }

        /**
         * {@inheritdoc}
         */
        protected function createTranslation()
        {
            return new SupplierTranslation();
        }
    }

4. Register your entity together with translation as a Sylius resource
----------------------------------------------------------------------

If you don't have it yet, create a file ``config/packages/sylius_resource.yaml``.

.. code-block:: yaml

    # config/packages/sylius_resource.yaml
    sylius_resource:
        resources:
            app.supplier:
                driver: doctrine/orm # You can use also different driver here
                classes:
                    model: App\Entity\Supplier
                translation:
                    classes:
                        model: App\Entity\SupplierTranslation

To check if the process was run correctly run such a command:

.. code-block:: bash

    $ php bin/console debug:container | grep supplier

The output should be:

.. image:: ../../_images/container_debug_supplier_translation.png
    :align: center

5. Update the database using migrations
---------------------------------------

Assuming that your database was up-to-date before adding the new entity, run:

.. code-block:: bash

    $ php bin/console doctrine:migrations:diff

This will generate a new migration file which adds the Supplier entity to your database.
Then update the database using the generated migration:

.. code-block:: bash

    $ php bin/console doctrine:migrations:migrate

6. Prepare new forms for your entity, that will be aware of its translation
---------------------------------------------------------------------------

You will need both ``SupplierType`` and ``SupplierTranslationType``.

Let's start with the translation type, as it will be included into the entity type.

.. code-block:: php

    <?php

    namespace App\Form\Type;

    use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;

    class SupplierTranslationType extends AbstractResourceType
    {
        /**
         * {@inheritdoc}
         */
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('name', TextType::class)
                ->add('description', TextareaType::class, [
                    'required' => false,
                ])
            ;
        }

        /**
         * {@inheritdoc}
         */
        public function getBlockPrefix()
        {
            return 'app_supplier_translation';
        }
    }

On the ``SupplierTranslationType`` we need to define only the translatable fields.

Then let's prepare the entity type, that will include the translation type.

.. code-block:: php

    <?php

    namespace App\Form\Type;

    use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
    use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
    use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
    use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;

    class SupplierType extends AbstractResourceType
    {
        /**
         * {@inheritdoc}
         */
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('translations', ResourceTranslationsType::class, [
                    'entry_type' => SupplierTranslationType::class,
                ])
                ->add('enabled', CheckboxType::class, [
                    'required' => false,
                ])
            ;
        }

        /**
         * {@inheritdoc}
         */
        public function getBlockPrefix()
        {
            return 'app_supplier';
        }
    }

7. Register the new forms as services
-------------------------------------

Before the newly created forms will be ready to use them, they need to be registered as services:

.. code-block:: yaml

    # config/services.yaml
    services:
        app.supplier.form.type:
            class: App\Form\Type\SupplierType
            tags:
                - { name: form.type }
            arguments: ['%app.model.supplier.class%', ['sylius']]
        app.supplier_translation.form.type:
            class: App\Form\Type\SupplierTranslationType
            tags:
                - { name: form.type }
            arguments: ['%app.model.supplier_translation.class%', ['sylius']]

8. Register the forms as resource forms of the Supplier entity
--------------------------------------------------------------

Extend the resource configuration of the ``app.supplier`` with forms:

.. code-block:: yaml

    # config/resources.yaml
    sylius_resource:
        resources:
            app.supplier:
                driver: doctrine/orm # You can use also different driver here
                classes:
                    model: App\Entity\Supplier
                    form: App\Form\Type\SupplierType
                translation:
                    classes:
                        model: App\Entity\SupplierTranslation
                        form: App\Form\Type\SupplierTranslationType

9. Define grid structure for the new entity
-------------------------------------------

To have templates for your Entity administration out of the box you can use Grids. Here you can see how to configure a grid for the Supplier entity.

.. code-block:: yaml

    # config/packages/_sylius.yaml
    sylius_grid:
        grids:
            app_admin_supplier:
                driver:
                    name: doctrine/orm
                    options:
                        class: App\Entity\Supplier
                fields:
                    name:
                        type: string
                        label: sylius.ui.name
                        sortable: translation.name
                    enabled:
                        type: twig
                        label: sylius.ui.enabled
                        options:
                            template: "@SyliusUi/Grid/Field/enabled.html.twig"
                actions:
                    main:
                        create:
                            type: create
                    item:
                        update:
                            type: update
                        delete:
                            type: delete

10. Create template
-------------------

.. code-block:: php

    # App/Resources/views/Supplier/_form.html.twig
    {% from '@SyliusAdmin/Macro/translationForm.html.twig' import translationForm %}

    {{ form_errors(form) }}
    {{ translationForm(form.translations) }}
    {{ form_row(form.enabled) }}

11. Define routing for entity administration
--------------------------------------------

Having a grid prepared we can configure routing for the entity administration:

.. code-block:: yaml

    # config/routes.yaml
    app_admin_supplier:
        resource: |
            alias: app.supplier
            section: admin
            templates: SyliusAdminBundle:Crud
            redirect: update
            grid: app_admin_supplier
            vars:
                all:
                    subheader: app.ui.supplier
                    templates:
                        form: App:Supplier:_form.html.twig
                index:
                    icon: 'file image outline'
        type: sylius.resource
        prefix: admin

12. Add entity administration to the admin menu
-----------------------------------------------

.. tip::

    See :doc:`how to add links to your new entity administration in the administration menu </customization/menu>`.

13. Check the admin panel for your changes
------------------------------------------

.. tip::

    To see what you can do with your new entity access the ``http://localhost:8000/admin/suppliers/`` url.

Learn more
----------

* :doc:`GridBundle documentation </components_and_bundles/bundles/SyliusGridBundle/index>`
* :doc:`ResourceBundle documentation </components_and_bundles/bundles/SyliusResourceBundle/index>`
* :doc:`Customization Guide </customization/index>`
