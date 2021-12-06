How to disable the order confirmation email?
============================================

In some usecases you may be wondering if it is possible to completely turn off the order confirmation email after the order complete.

This is a complicated situation, because we need to be precise what is our expected result:

* `to disable that email in the system completely <#disabling-the-email-in-the-configuration>`_,
* `to send a different email on the complete action of an order instead of the order confirmation email <#disabling-the-listener-responsible-for-that-action>`_,

Below a few ways to disable that email are presented:

Disabling the email in the configuration
----------------------------------------

There is a pretty straightforward way to disable an e-mail using just a few lines of yaml:

.. code-block:: yaml

    # config/packages/sylius_mailer.yaml
    sylius_mailer:
        emails:
            order_confirmation:
                enabled: false

That's all. With that configuration the order confirmation email will not be sent.

Disabling the listener responsible for that action
--------------------------------------------------

To easily turn off the sending of the order confirmation email you will need to disable the ``OrderCompleteListener`` service.
This can be done via a CompilerPass.

.. code-block:: php

    <?php

    namespace App\DependencyInjection\Compiler;

    use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
    use Symfony\Component\DependencyInjection\ContainerBuilder;

    final class MailPass implements CompilerPassInterface
    {
        public function process(ContainerBuilder $container): void
        {
            $container->removeDefinition('sylius.listener.order_complete');
        }
    }

The above compiler pass needs to be added to your kernel in the ``src/Kernel.php`` file:

.. code-block:: php

    <?php

    namespace App;

    use App\DependencyInjection\Compiler\MailPass;
    // ...

    final class Kernel extends BaseKernel
    {
        // ...

        public function build(ContainerBuilder $container): void
        {
            parent::build($container);

            $container->addCompilerPass(new MailPass());
        }
    }

That's it, we have removed the definition of the listener that is responsible for sending the order confirmation email.

Learn more
----------

* `Compiler passes in the Symfony documentation <https://symfony.com/doc/current/service_container/compiler_passes.html>`_
