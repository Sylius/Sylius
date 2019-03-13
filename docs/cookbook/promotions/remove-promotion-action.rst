How to remove a predefined promotion action?
============================================

Some promotion can be inappropriate to some business. Here is method to remove promotion actions in a compiler pass.

Add a compiler pass into your project
-------------------------------------

In your kernel project class, you can add a custom compiler pass.

.. code-block:: php

    <?php

    namespace App;

    use App\DependencyInjection\Compiler\PromotionPass;

    final class Kernel extends BaseKernel
    {
        // ...

        protected function build(ContainerBuilder $container): void
        {
            $container->addCompilerPass(new PromotionPass());
        }
    }

Remove project rule
-------------------

.. code-block:: php

    <?php

    namespace App\DependencyInjection\Compiler;

    use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
    use Symfony\Component\DependencyInjection\ContainerBuilder;

    final class PromotionActionPass implements CompilerPassInterface
    {
        public function process(ContainerBuilder $container): void
        {
            $parameter = $container->getParameter('sylius.promotion_actions');

            $actionsToRemove = [
                'shipping_percentage_discount',
            ];

            foreach ($actionsToRemove as $actionToRemove) {
                $definition = $container->getDefinition(sprintf('sylius.promotion_action..%s', $actionToRemove));
                $type = $definition->getClass()::TYPE;
                unset($parameter[$type]);
            }

            $container->setParameter('sylius.promotion_actions', $parameter);
        }
    }
