Custom Filter
=============

Sylius Grids come with built-in filters, but there are use-cases where you need something more than basic filter. Grids allow you to define your own filter types!

To add a new filter, we need to create an appropriate class and form type.

.. code-block:: php

    <?php

    namespace AppBundle\Grid\Filter;

    use Sylius\Component\Grid\Data\DataSourceInterface;
    use Sylius\Component\Grid\Filtering\FilterInterface;

    class SuppliersStatisticsFilter implements FilterInterface
    {
        public function apply(DataSourceInterface $dataSource, $name, $data, array $options = [])
        {
            // Your filtering logic. DataSource is kind of query builder.
            // $data['stats'] contains the submitted value!
            // here is an example
            $dataSource->restrict($dataSource->getExpressionBuilder()->equal('stats', $data['stats']));
        }
    }

And the form type:

.. code-block:: php

    <?php

    namespace AppBundle\Form\Type\Filter;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    class SuppliersStatisticsFilterType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder->add(
                'stats',
                ChoiceType:class,
                ['choices' => range($options['range'][0], $options['range'][1])]
            );
        }

        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver
                ->setDefaults([
                    'range' => [0, 10],
                ])
                ->setAllowedTypes('range', ['array'])
            ;
        }
    }

Create a template for the filter, similar to the existing ones:

.. code-block:: html

    # app/Resources/views/Grid/Filter/suppliers_statistics.html.twig
    {% form_theme form 'SyliusUiBundle:Form:theme.html.twig' %}

    {{ form_row(form) }}

That is all. Now let's register your new filter type as service.

.. code-block:: yaml

    # app/config/services.yml

    services:
        app.grid.filter.suppliers_statistics:
            class: AppBundle\Grid\Filter\SuppliersStatisticsFilter
            tags:
                -
                    name: sylius.grid_filter
                    type: suppliers_statistics
                    form-type: AppBundle\Form\Type\Filter\SuppliersStatisticsFilterType

Now you can use your new filter type in the grid configuration!

.. code-block:: yaml

    sylius_grid:
        grids:
            app_tournament:
                driver: doctrine/orm
                resource: app.tournament
                filters:
                    stats:
                        type: suppliers_statistics
                        options:
                            range: [0, 100]
        templates:
            filter:
                suppliers_statistics: 'AppBundle:Grid/Filter:suppliers_statistics.html.twig'
