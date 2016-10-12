Custom Field Type
=================

There are certain cases when built-in field types are not enough. Sylius Grids allows to define new types with ease!

All you need to do is create your own class implementing FieldTypeInterface and register it as a service.

.. code-block:: php

    <?php

    namespace AppBundle\Grid\FieldType;

    use Sylius\Component\Grid\Definition\Field;
    use Sylius\Component\Grid\FieldTypes\FieldTypeInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    class CustomType implements FieldTypeInterface
    {
        public function render(Field $field, $data, array $options = [])
        {
            // Your rendering logic... Use Twig, PHP or even external api...
        }

        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver
                ->setDefaults([
                    'dynamic' => false
                ])
                ->setAllowedTypes([
                    'dynamic' => ['boolean']
                ])
            ;
        }

        public function getName()
        {
            return 'custom';
        }
    }

That is all. Now register your new field type as a service.

.. code-block:: yaml

    # app/config/services.yml
    app.grid_field.custom:
        class: AppBundle\Grid\FieldType\CustomType
        tags:
            - { name: sylius.grid_field, type: custom }

Now you can use your new column type in the grid configuration!

.. code-block:: yaml

    sylius_grid:
        grids:
            app_admin_supplier:
                driver:
                    name: doctrine/orm
                    options:
                        class: AppBundle\Entity\Supplier
                fields:
                    name:
                        type: custom
                        label: sylius.ui.name
