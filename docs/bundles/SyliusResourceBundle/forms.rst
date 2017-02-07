Forms
=====

Have you noticed how Sylius generates forms for you? Of course, for many use-cases you may want to create a custom form.

Custom Resource Form
--------------------

Create a FormType class for your resource
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: php

    <?php

    namespace AppBundle\Form\Type;

    use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
    use Symfony\Component\Form\FormBuilderInterface;

    class BookType extends AbstractResourceType
    {
        /**
         * {@inheritdoc}
         */
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            // Build your custom form, with all fields that you need
            $builder->add('title', TextType::class);
        }

        /**
         * {@inheritdoc}
         */
        public function getBlockPrefix()
        {
            return 'app_book';
        }
    }

.. note::

    The getBlockPrefix method returns the prefix of the template block name for this type.

Register the FormType as a service
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. warning::

    the registration of a form type is only needed when the form is extending the ``AbstractResourceType``
    or when it has some custom constructor dependencies.

.. code-block:: yaml

    app.book.form.type:
        class: AppBundle\Form\Type\BookType
        tags:
            - { name: form.type }
        arguments: ['%app.model.book.class%', '%app.book.form.type.validation_groups%']

Configure the form for your resource
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: yaml

    sylius_resource:
        resources:
            app.book:
                classes:
                    model: AppBundle\Entity\Book
                    form: AppBundle\Form\Type\BookType

That's it. Your new class will be used for all forms!
