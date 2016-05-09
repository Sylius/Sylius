Custom Filter
=============

Sylius grids come with a lot of built-in filters, but there are use-cases where you need something more than basic filter. Grids allow you to define your own filter types!

To add a new filter, we need to create appropriate class and form type.

.. code-block:: php

    <?php

    namespace App\Grid\Filter;

    use Sylius\Component\Grid\Data\DataSourceInterface;
    use Sylius\Component\Grid\Filter\FilterInterface;
    use Symfony\Component\OptionsResolver\OptionsResolverInterface;

    class TournamentStatisticsFilter implements FilterInterface
    {
        public function apply(DataSourceInterface $dataSource, $data, array $options = array())
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
            return 'tournament_statistics'
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

        public function getType()
        {
            return 'sylius_filter_tournament_statistics'; // The name is important to be sylius_filter_NAME
        }
    }

That is all. Now let register your new filter type as service.

.. code-block:: yaml

    # app/config/services.yml

    services:
        app.grid.filter.tournament_statistics:
            class: App\Grid\Filter\TournamentStatisticsFilter
            tags:
                - { name: sylius.grid_filter, type: tournament_statistics }
        app.form.type.filter.tournament_statistics:
            class: AppBundle\Form\Type\Filter\TournamentStatisticsFilterType
            tags:
                - { name: form.type, alias: sylius_filter_tournament_statistics }


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
