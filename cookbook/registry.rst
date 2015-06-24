Using the registry in your bundle
=================================

We will to show you how to set up the sylius registry. In this example, we will register two price calculators.

.. code-block:: xml

    <!-- Resources/config/services.xml -->

    <?xml version="1.0" ?>

    <container xmlns="http://symfony.com/schema/dic/services"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">
        <parameters>
            <parameter key="app.price.calculator.registry.class">App\Component\Registry\ServiceRegistry</parameter>
            <parameter key="app.price.calculator.interface">App\Bundle\MyBundle\PriceCalculatorInterface</parameter>
        </parameters>

        <services>
            <!-- You need to declare the registry as a service, it expect as argument the type of object that you want to register -->
            <service id="app.price.calculator.registry" class="%app.price.calculator.registry.class%">
                <argument>%app.price.calculator.interface%</argument>
            </service>

            <!-- Don't forget that a registry don't create object. You need to create them before and register them into the registry after -->
            <service id="app.price.calculator.default" class="...">
            <service id="app.price.calculator.custom" class="...">
        </services>
    </container>

.. code-block:: php

    namespace App\Bundle\MyBundle\DependencyInjection\Compiler;

    class RegisterCalculatorServicePass implements CompilerPassInterface
    {
        public function process(ContainerBuilder $container)
        {
            // You can check if your registry is defined
            if (!$container->hasDefinition('app.price.calculator.registry')) {
                return;
            }

            $registryDefinition = $container->getDefinition('app.price.calculator.registry');

            // You can register your services like this
            $registryDefinition->addMethodCall(
                'register',
                array(
                    'default',
                    new Reference('app.price.calculator.default'),
                )
            );

            $registryDefinition->addMethodCall(
                'register',
                array(
                    'custom',
                    new Reference('app.price.calculator.default'),
                )
            );
        }
    }

Finally, you need to register your custom pass with the container

.. code-block:: php

    namespace App\Bundle\MyBundle;
    
    use App\Bundle\MyBundle\DependencyInjection\Compiler\RegisterCalculatorServicePass;

    class AppMyBundleBundle extends Bundle
    {
        public function build(ContainerBuilder $container)
        {
            parent::build($container);

            $container->addCompilerPass(new RegisterCalculatorServicePass());
        }
    }
