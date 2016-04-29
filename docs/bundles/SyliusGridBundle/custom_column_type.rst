Custom Column Type
==================

There are certain cases when built-in column types are not enough. Sylius grid allows to define new types with ease!

All you need to do is implement your own class implementing ColumnTypeInterface and registering it in the container.

.. code-block:: php

    <?php

    namespace App\Grid\ColumnType;

    use Sylius\Component\Grid\ColumnType\ColumnTypeInterface;
    use Symfony\Component\OptionsResolver\OptionsResolverInterface;

    class TournamentMonitorType implements ColumnTypeInterface
    {
        public function render($data, array $options = array())
        {
            // Your rendering logic... Use Twig, PHP or even external api...
            if ($options['dynamic']) {
                $output = // ...
            }

            return $output;
        }

        public function setOptions(OptionsResolverInterface $resolver)
        {
            $resolver
                ->setDefaults(array(
                    'dynamic' => false
                ))
                ->setAllowedTypes(array(
                    'dynamic' => array('boolean')
                ))
            ;
        }

        public function getType()
        {
            return 'tournament_monitor'
        }
    }

That is all. Now let register your new column type as service.

.. code-block:: yaml

    # app/config/services.yml

    services:
        app.grid.column_type.tournament_monitor:
            class: App\Grid\ColumnType\TournamentMonitorType
            tags:
                - { name: sylius.grid_column_type type: tournament_monitor }

Now you can use your new column type in the grid configuration!

.. code-block:: yaml

    sylius_grid:
        grids:
            app_tournament:
                driver: doctrine/orm
                resource: app.tournament
                columns:
                    monitor:
                        type: tournament_monitor
                        options:
                            dynamic: "%kernel.debug%"
