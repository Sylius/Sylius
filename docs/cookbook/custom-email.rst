How to send a custom e-mail?
============================

.. note::

    This cookbook is suitable for a clean :doc:`sylius-standard installation </book/installation/installation>`.
    For more general tips, while using :doc:`SyliusMailerBundle </bundles/SyliusMailerBundle/index>`
    go to `Sending configurable e-mails in Symfony Blogpost <http://sylius.org/blog/sending-configurable-e-mails-in-symfony>`_.

Currently **Sylius** is sending e-mails only in a few "must-have" cases - see :doc:`E-mails documentation </book/architecture/emails>`.
Of course these cases may not be sufficient for your business needs. If so, you will need to create your own custom e-mails inside the system.

On a basic example we will now teach how to do it.

Let's assume that you would like such a feature in your system:

.. code-block:: text

    Feature: Sending a notification email to the administrator when a product is out of stock
        In order to be aware which products become out of stock
        As an Administrator
        I want to be notified via email when products become out of stock

To achieve that you will need to:

1. Create a new e-mail that will be sent:
-----------------------------------------

* prepare a template for your email in the ``app/Resources/views/Email``.

.. code-block:: twig

    {# app/Resources/views/Email/out_of_stock.html.twig #}
    {% block subject %}
        One of your products has become out of stock.
    {% endblock %}

    {% block body %}
        {% autoescape %}
            The {{ variant.name }} variant is out of stock!
        {% endautoescape %}
    {% endblock %}

* configure the email under ``sylius_mailer:`` in the ``app/config/emails.yml`` included in ``app/config/yml``.

.. code-block:: yaml

    # app/config/emails.yml
    sylius_mailer:
        sender:
            name: Example.com
            address: no-reply@example.com
        emails:
            out_of_stock:
                subject: "A product has become out of stock!"
                template: "AppBundle:Email:out_of_stock.html.twig"

.. code-block:: yaml

    # app/config/config.yml
    imports:
        - { resource: "emails.yml" }

2. Create an Email Manager class:
---------------------------------

* It will need the **EmailSender**, the **AvailabilityChecker** and the **AdminUser Repository**.
* It will operate on the **Order** where it needs to check each OrderItem, get their **ProductVariants** and check if they are available.

.. code-block:: php

    <?php

    namespace AppBundle\EmailManager;

    use Sylius\Component\Core\Model\OrderInterface;
    use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
    use Sylius\Component\Mailer\Sender\SenderInterface;
    use Sylius\Component\Resource\Repository\RepositoryInterface;

    class OutOfStockEmailManager
    {
        /**
         * @var SenderInterface
         */
        private $emailSender;

        /**
         * @var AvailabilityCheckerInterface $availabilityChecker
         */
        private $availabilityChecker;

        /**
         * @var RepositoryInterface $adminUserRepository
         */
        private $adminUserRepository;

        /**
         * @param SenderInterface $emailSender
         * @param AvailabilityCheckerInterface $availabilityChecker
         * @param RepositoryInterface $adminUserRepository
         */
        public function __construct(
            SenderInterface $emailSender,
            AvailabilityCheckerInterface $availabilityChecker,
            RepositoryInterface $adminUserRepository
        ) {
            $this->emailSender = $emailSender;
            $this->availabilityChecker = $availabilityChecker;
            $this->adminUserRepository = $adminUserRepository;
        }

        /**
         * @param OrderInterface $order
         */
        public function sendOutOfStockEmail(OrderInterface $order)
        {
            // get all admins, but remember to put them into an array
            $admins = $this->adminUserRepository->findAll()->toArray();

            foreach($order->getItems() as $item) {
                $variant = $item->getVariant();

                $stockIsSufficient = $this->availabilityChecker->isStockSufficient($variant, 1);

                if ($stockIsSufficient) {
                    continue;
                }
                foreach($admins as $admin) {
                    $this->emailSender->send('out_of_stock', $admin->getEmail(), ['variant' => $variant]);
                }
            }
        }
    }

3. Register the manager as a service:
-------------------------------------

.. code-block:: yaml

    # app/config/services.yml
    services:
        app.email_manager.out_of_stock:
        class: AppBundle\EmailManager\OutOfStockEmailManager
        arguments: ['@sylius.email_sender', '@sylius.availability_checker', '@sylius.repository.admin_user']

4. Customize the state machine callback of Order's Payment:
-----------------------------------------------------------

.. code-block:: yaml

    # app/config/state_machine.yml
    winzou_state_machine:
        sylius_order_payment:
            callbacks:
                after:
                    app_out_of_stock_email:
                        on: ["pay"]
                        do: ["@app.email_manager.out_of_stock", "sendOutOfStockEmail"]
                        args: ["object"]

.. code-block:: yaml

    # app/config/config.yml
    imports:
        - { resource: "state_machine.yml" }

**Done!**

Learn More
----------

* :doc:`Emails Concept </book/architecture/emails>`
* :doc:`State Machine Concept </book/architecture/state_machine>`
* :doc:`Customization Guide - State Machine </customization/state_machine>`
* `Sending configurable e-mails in Symfony Blogpost <http://sylius.org/blog/sending-configurable-e-mails-in-symfony>`_
