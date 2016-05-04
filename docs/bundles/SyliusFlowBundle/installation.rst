Installation
============

We assume you're familiar with `Composer <http://packagist.org>`_, a dependency manager for PHP.
Use the following command to add the bundle to your `composer.json` and download package.

If you have `Composer installed globally <http://getcomposer.org/doc/00-intro.md#globally>`_.

.. code-block:: bash

    $ composer require sylius/flow-bundle

Otherwise you have to download .phar file.

.. code-block:: bash

    $ curl -sS https://getcomposer.org/installer | php
    $ php composer.phar require sylius/flow-bundle

Adding required bundles to the kernel
-------------------------------------

First, you need to enable the bundle inside the kernel.
If you're not using any other Sylius bundles, you will also need to add `SyliusResourceBundle` and its dependencies to the kernel.
Don't worry, everything was automatically installed via Composer.

.. code-block:: php

    <?php

    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            new Sylius\Bundle\FlowBundle\SyliusFlowBundle(),

            // Other bundles...
        );
    }

Creating your steps
-------------------

We will create a very simple wizard now, without forms, storage, to keep things simple and get started fast.

Lets create a few simple steps:

.. code-block:: php

    <?php

    namespace Acme\DemoBundle\Process\Step;

    use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
    use Sylius\Bundle\FlowBundle\Process\Step\AbstractControllerStep;

    class FirstStep extends AbstractControllerStep
    {
        public function displayAction(ProcessContextInterface $context)
        {
            return $this->render('AcmeDemoBundle:Process/Step:first.html.twig');
        }

        public function forwardAction(ProcessContextInterface $context)
        {
            return $this->complete();
        }
    }


.. code-block:: php

    <?php

    namespace Acme\DemoBundle\Process\Step;

    use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
    use Sylius\Bundle\FlowBundle\Process\Step\AbstractControllerStep;

    class SecondStep extends AbstractControllerStep
    {
        public function displayAction(ProcessContextInterface $context)
        {
            return $this->render('AcmeDemoBundle:Process/Step:second.html.twig');
        }

        public function forwardAction(ProcessContextInterface $context)
        {
            return $this->complete();
        }
    }

And so on, one class for each step in the wizard.

As you can see, there are two actions in each step, display and forward.
Usually, there is a form in a forward action where you can pick some data.
When you do ``return $this->complete()`` the wizard will take you to the next step.

Creating scenario
-----------------

To group steps into the wizard, we will implement *ProcessScenarioInterface*:

.. code-block:: php

    <?php

    namespace Acme\DemoBundle\Process;

    use Sylius\Bundle\FlowBundle\Process\Builder\ProcessBuilderInterface;
    use Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface;
    use Symfony\Component\DependencyInjection\ContainerAware;
    use Acme\DemoBundle\Process\Step;

    class SyliusScenario extends ContainerAware implements ProcessScenarioInterface
    {
        public function build(ProcessBuilderInterface $builder)
        {
            $builder
                ->add('first', new Step\FirstStep())
                ->add('second', new Step\SecondStep())
                // ...
            ;
        }
    }

As you can see, we just add each step to process builder with a desired name.
The name will be used in the routes to navigate to particular step.

Registering scenario
--------------------

In order for this to work, we need to register `SyliusScenario` and tag it as ``sylius.process.scenario``:

.. code-block:: xml

    <service id="sylius.scenario.flow" class="Acme\DemoBundle\Process\SyliusScenario">
        <call method="setContainer">
            <argument type="service" id="service_container" />
        </call>
        <tag name="sylius.process.scenario" alias="acme_flow" />
    </service>

The configured alias will be used later in the route parameters to identify the scenario as you can have more then one.

Routing configuration
---------------------

Import routing configuration:

.. code-block:: yaml

    acme_flow:
        resource: "@SyliusFlowBundle/Resources/config/routing.yml"
        prefix: /flow

If you take a look into imported routing configuration, you will see that ``sylius_flow_start`` is a wizard entry point.
``sylius_flow_display`` displays the step with the given name, ``sylius_flow_forward`` forwards to the next step from the step with the given name.
All routes have an `scenarioAlias` as a required parameter to identify the scenario.

Templates
---------

Step templates are like any other action template, usually due to the nature of multi-step wizards, they have back and forward buttons:

.. code-block:: jinja

    <h1>Welcome to second step</h1>
    <a href="{{ path('sylius_flow_display', {'scenarioAlias': 'acme_flow', 'stepName': 'first'}) }}" class="btn btn-success"><i class="icon-backward icon-white"></i> back</a>
    <a href="{{ path('sylius_flow_forward', {'scenarioAlias': 'acme_flow', 'stepName': 'second'}) }}" class="btn btn-success">forward <i class="icon-forward icon-white"></i></a>
