Forms
=====

Have you noticed how Sylius generates forms for you? Of course, for many use-cases you may want to create a custom form.

Custom Resource Form
--------------------

You need to create a simple class:

.. code-block:: php

    <?php

    // src/AppBundle/Form/Type/BookType.php

    namespace AppBundle\Form\Type;

    use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
    use Symfony\Component\Form\FormBuilderInterface;

    class BookType extends AbstractResourceType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            // Build your custom form!
            $builder->add('title', 'text');
        }

        public function getName()
        {
            return 'app_book';
        }
    }

Now, configure it under ``sylius_resource``:

.. code-block:: yaml

    sylius_resource:
        resources:
            app.book:
                classes:
                    model: AppBundle\Entity\Book
                    form:
                        default: AppBundle\Form\Type\BookType

That's it. Your new class will be used for all forms!
