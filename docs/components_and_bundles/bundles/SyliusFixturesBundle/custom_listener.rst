Custom listener
===============

Basic listener
--------------

Let's create a listener that removes the directory before loading the fixtures.

.. code-block:: php

    namespace AppBundle\Listener;

    use Sylius\Bundle\FixturesBundle\Listener\AbstractListener;
    use Sylius\Bundle\FixturesBundle\Listener\BeforeSuiteListenerInterface;
    use Sylius\Bundle\FixturesBundle\Listener\SuiteEvent;
    use Symfony\Component\Filesystem\Filesystem;

    final class DirectoryPurgerListener extends AbstractListener implements ListenerInterface
    {
        public function getName()
        {
            return 'directory_purger';
        }

        public function beforeSuite(SuiteEvent $suiteEvent, array $options)
        {
            (new Filesystem())->remove('/hardcoded/path/to/directory');
        }
    }

The next step is to register this listener:

.. code-block:: xml

    <service id="app.listener.directory_purger" class="AppBundle\Listener\DirectoryPurgerListener">
        <tag name="sylius_fixtures.listener" />
    </service>


Listener is now registered and ready to use in your suite:

.. code-block:: yaml

    sylius_fixtures:
        suites:
            my_suite:
                listeners:
                    directory_purger: ~

Configurable listener
---------------------

Listener that removes a hardcoded directory isn't very useful. Allowing it to receive an array of directories would make
this listener a lot more reusable.

.. code-block:: php

    // ...

    final class DirectoryPurgerListener extends AbstractListener implements ListenerInterface
    {
        // ...

        public function beforeSuite(SuiteEvent $suiteEvent, array $options)
        {
            (new Filesystem())->remove($options['directories']);
        }

        protected function configureOptionsNode(ArrayNodeDefinition $optionsNode)
        {
            $optionsNodeBuilder
                ->arrayNode('directories')
                    ->performNoDeepMerging()
                    ->prototype('scalar')
            ;
        }
    }

.. note::

    The ``AbstractListener`` implements the ``ConfigurationInterface::getConfigTreeBuilder()`` and exposes a handy
    ``configureOptionsNode()`` method to reduce the boilerplate. It is possible to test this configuration
    using `SymfonyConfigTest`_ library.

Now, it is possible to remove different directories in each suite:

.. code-block:: yaml

    sylius_fixtures:
        suites:
            my_suite:
                listener:
                    directory_purger:
                        options:
                            directories:
                                - /custom/directory
                                - /another/custom/directory
            my_another_suite:
                listener:
                    directory_purger:
                        options:
                            directories:
                                - /path/per/suite

.. _`SymfonyConfigTest`: https://github.com/matthiasnoback/SymfonyConfigTest
