Custom Filter
=============

Sylius Grids come with built-in filters, but there are use-cases where you need something more than basic filter. Grids allow you to define your own filter types!

To add a new filter, we need to create an appropriate class and form type.

.. code-block:: php

    <?php

    namespace App\Grid\Filter;

    use Sylius\Component\Grid\Data\DataSourceInterface;
    use Sylius\Component\Grid\Filtering\FilterInterface;
    use Symfony\Component\OptionsResolver\OptionsResolverInterface;

    class SuppliersStatisticsFilter implements FilterInterface
    {
        public function apply(DataSourceInterface $dataSource, $name, $data, array $options = array())
        {
            // Your filtering logic. DataSource is kind of query builder.
            // $data['stats'] contains the submitted value!
        }

        public function setOptions(OptionsResolverInterface $resolver)
        {
            $resolver
                ->setDefaults(array(
                    'range' => array(0, 10)
                ))
                ->setAllowedTypes(array(
                    'range' => array('array')
                ))
            ;
        }

        public function getType()
        {
            return 'supplier_statistics';
        }
    }

And the form type:

.. code-block:: php

    <?php

    namespace AppBundle\Form\Type\Filterh;

    use Sylius\Component\Grid\Data\DataSourceInterface;
    use Sylius\Component\Grid\Filter\FilterInterface;
    use Symfony\Component\OptionsResolver\OptionsResolverInterface;

    class TournamentStatisticsFilterType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder->add('stats', 'choice', array('choices' => range($options['range'][0], $options['range'][1])));
        }

        public function setDefaultOptions(OptionsResolverInterface $resolver)
        {
            $resolver
                ->setDefaults(array(
                    'range' => array(0, 10)
                ))
                ->setAllowedTypes(array(
                    'range' => array('array')
                ))
            ;
        }

        public function getName()
        {
            return 'sylius_filter_tournament_statistics'; // The name is important to be sylius_filter_NAME
        }
    }

Create a template for the filter, similar to the existing ones:

.. code-block:: html

    # AppBundle/Resources/views/Grid/Filter/suppliers_statistics.html.twig
    {% form_theme form 'SyliusUiBundle:Form:theme.html.twig' %}

    {{ form_row(form) }}

That is all. Now let's register your new filter type as service.

.. code-block:: yaml

    # app/config/services.yml

    services:
        app.grid.filter.suppliers_statistics:
            class: AppBundle\Grid\Filter\SuppliersStatisticsFilter
            tags:
                - { name: sylius.grid_filter, type: suppliers_statistics }
        app.form.type.grid.filter.suppliers_statistics:
            class: AppBundle\Form\Type\Filter\SuppliersStatisticsFilterType
            tags:
                - { name: form.type, alias: sylius_grid_filter_suppliers_statistics }

Now you can use your new filter type in the grid configuration!

.. code-block:: yaml

    sylius_grid:
        grids:
            app_tournament:
                driver: doctrine/orm
                resource: app.tournament
                filters:
                    stats:
                        type: tournament_statistics
                        options:
                            range: [0, 100]
        templates:
            filter:
                suppliers_statistics: "AppBundle:Grid/Filter:suppliers_statistics.html.twig"
