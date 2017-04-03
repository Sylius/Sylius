How to disable the order confirmation email?
============================================

In some usecases you may be wondering if it is possible to completely turn off the order confirmation email after the order complete.

This is a complicated situation, because we need to be precise what is our expected result:

* to disable that email in the system completely,
* to send a different email on the complete action of an order instead of the order confirmation email,
* to send the order confirmation email in a different moment.

Below a few ways to disable that email are presented:

Disabling the email in the configuration
----------------------------------------

There is a pretty straightforward way to disable an e-mail using just a few lines of yaml:

.. code-block:: yaml

    # app/config/config.yml
    sylius_mailer:
        emails:
            order_confirmation:
                enabled: false

That's all. With that configuration placed in your ``app/config/config.yml`` the order confirmation email will not be sent.

Disabling the listener responsible for that action
--------------------------------------------------

To easily turn off the sending of the order confirmation email you will need to disable the ``OrderCompleteListener`` service.
This can be done via a CompilerPass.

.. code-block:: php

    <?php

    namespace AppBundle\DependencyInjection\Compiler;

    use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
    use Symfony\Component\DependencyInjection\ContainerBuilder;

    class MailPass implements CompilerPassInterface
    {
        public function process(ContainerBuilder $container)
        {
            $container->removeDefinition('sylius.listener.order_complete');
        }
    }

The above compiler pass needs to be added to your bundle in the ``AppBundle/AppBundle.php`` file:

.. code-block:: php

    <?php

    namespace AppBundle;

    use AppBundle\DependencyInjection\Compiler\MailPass;
    use Symfony\Component\HttpKernel\Bundle\Bundle;
    use Symfony\Component\DependencyInjection\ContainerBuilder;

    class AppBundle extends Bundle
    {
        public function build(ContainerBuilder $container)
        {
            parent::build($container);

            $container->addCompilerPass(new MailPass());
        }
    }

That's it, we have removed the definition of the listner that is responsible for sending the order confirmation email.

Overriding service definition
-----------------------------

If you'd like to change the logic, and for instance send a confirmation email not after the order complete, but after the payment is complete
then you just need to override the service definition, and change the event to which the listener is attached.

.. code-block:: yaml

    services:
        sylius.listener.order_complete:
            class: Sylius\Bundle\ShopBundle\EventListener\OrderCompleteListener
            tags:
                - { name: kernel.event_listener, event: sylius.order.payment.post_complete, method: sendConfirmationEmail }

Learn more
----------

* `Compiler passes in the Symfony documentation <http://symfony.com/doc/current/service_container/compiler_passes.html>`_
