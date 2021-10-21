How to customize the refund form?
=================================

.. note::

    This cookbook describes customization of a feature available only with `Sylius/RefundPlugin <https://github.com/Sylius/RefundPlugin/>`_ installed.

A refund form is the form in which, as an Administrator, you can specify the exact amounts of money that will be refunded to a Customer.

Why would you customize the refund form?
----------------------------------------

Refund Plugin provides a generic solution for refunding orders, it is enough for a basic refund but many shops need more custom functionalities.
For example, one may need to add refund payments scheduling, as they may be paid once a month.

How to add a field to the refund form?
--------------------------------------

The refund form is a form used to create the Refund Payment, thus in order to add a field to this form,
you need to first add it to the Refund Payment's model.

Refunds are processed with such a flow: ``command -> handler -> event -> listener``, and this flow we will also need to customize in order to process the data from the new field.

In this customization, we will be extending the refund form with a ``scheduledAt`` field,
which might be used then for scheduling the payments in the payment gateway.

**1. Add the custom field to the Refund Payment:**

Extended refund payment should look like this:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Entity\Refund;

    use Doctrine\ORM\Mapping as ORM;
    use Sylius\RefundPlugin\Entity\RefundPayment as BaseRefundPayment;

    /**
     * @ORM\Entity
     * @ORM\Table(name="sylius_refund_refund_payment")
     */
    class RefundPayment extends BaseRefundPayment implements RefundPaymentInterface
    {
        /**
         * @var \DateTimeInterface|null
         *
         * @ORM\Column(type="datetime", nullable="true", name="scheduled_at")
         */
        protected $scheduledAt;

        public function getScheduledAt(): ?\DateTimeInterface
        {
            return $this->scheduledAt;
        }

        public function setScheduledAt(\DateTimeInterface $scheduledAt): void
        {
            $this->scheduledAt = $scheduledAt;
        }
    }

It should implement a new interface:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Entity\Refund;

    use Sylius\RefundPlugin\Entity\RefundPaymentInterface as BaseRefundPaymentInterface;

    interface RefundPaymentInterface extends BaseRefundPaymentInterface
    {
        public function getScheduledAt(): ?\DateTimeInterface;

        public function setScheduledAt(\DateTimeInterface $date): void;
    }

Remember to update resource configuration:

.. code-block:: yaml

    # config/packages/sylius_refund.yaml
    sylius_resource:
        resources:
            sylius_refund.refund_payment:
                classes:
                    model: App\Entity\Refund\RefundPayment
                    interface: App\Entity\Refund\RefundPaymentInterface

And update the database:

.. code-block:: bash

    php bin/console doctrine:migrations:diff
    php bin/console doctrine:migrations:migrate

**2. Modify the refund form:**

Once we have the new field on the Refund Payment, we will need to display its input on the refund form.
We need to overwrite the template ``orderRefunds.html.twig`` from Refund Plugin.
To achieve that copy the entire ``orderRefunds.html.twig`` to ``templates/bundles/SyliusRefundPlugin/orderRefunds.html.twig``:

.. code-block:: bash

    mkdir templates/bundles/SyliusRefundPlugin
    cp vendor/sylius/refund-plugin/src/Resources/views/orderRefunds.html.twig templates/bundles/SyliusRefundPlugin

Then add:

.. code-block:: twig

    <div class="field">
        <label for="scheduled-at">Scheduled at</label>
        <input type="date" name="sylius_scheduled_at" id="scheduled-at" />
    </div>

**3. Adjust the ``RefundUnits`` command:**

We want the refund payments to be created with our extra ``scheduledAt`` date, therefore we need to provide this data in command,
We will extend the ``RefundUnits`` command from Refund Plugin and add the new value:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Command;

    use Sylius\RefundPlugin\Command\RefundUnits as BaseRefundUnits;

    final class RefundUnits extends BaseRefundUnits
    {
        /** @var \DateTimeInterface|null */
        private $scheduledAt;

        public function __construct(
            string $orderNumber,
            array $units,
            array $shipments,
            int $paymentMethodId,
            string $comment,
            ?\DateTimeInterface $scheduledAt
        ) {
            parent::__construct($orderNumber, $units, $shipments, $paymentMethodId, $comment);

            $this->scheduledAt = $scheduledAt;
        }

        public function getScheduledAt(): ?\DateTimeInterface
        {
            return $this->scheduledAt;
        }

        public function setScheduledAt(?\DateTimeInterface $scheduledAt): void
        {
            $this->scheduledAt = $scheduledAt;
        }
    }

**4. Update the ``RefundUnitsCommandCreator``:**

The controller related to the refund form dispatches the ``RefundUnits`` command, and there is a service that creates a command from request,
so we need to overwrite the ``Sylius\RefundPlugin\Creator\RefundUnitsCommandCreator``:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Creator;

    use App\Command\RefundUnits;
    use Sylius\RefundPlugin\Command\RefundUnits as BaseRefundUnits;
    use Sylius\RefundPlugin\Converter\RefundUnitsConverterInterface;
    use Sylius\RefundPlugin\Creator\RefundUnitsCommandCreatorInterface;
    use Sylius\RefundPlugin\Exception\InvalidRefundAmount;
    use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
    use Sylius\RefundPlugin\Model\RefundType;
    use Sylius\RefundPlugin\Model\ShipmentRefund;
    use Symfony\Component\HttpFoundation\Request;
    use Webmozart\Assert\Assert;

    final class RefundUnitsCommandCreator implements RefundUnitsCommandCreatorInterface
    {
        /** @var RefundUnitsConverterInterface */
        private $refundUnitsConverter;

        public function __construct(RefundUnitsConverterInterface $refundUnitsConverter)
        {
            $this->refundUnitsConverter = $refundUnitsConverter;
        }

        public function fromRequest(Request $request): BaseRefundUnits
        {
            Assert::true($request->attributes->has('orderNumber'), 'Refunded order number not provided');

            $units = $this->refundUnitsConverter->convert(
                $request->request->has('sylius_refund_units') ? $request->request->all()['sylius_refund_units'] : [],
                RefundType::orderItemUnit(),
                OrderItemUnitRefund::class
            );

            $shipments = $this->refundUnitsConverter->convert(
                $request->request->has('sylius_refund_shipments') ? $request->request->all()['sylius_refund_shipments'] : [],
                RefundType::shipment(),
                ShipmentRefund::class
            );

            if (count($units) === 0 && count($shipments) === 0) {
                throw InvalidRefundAmount::withValidationConstraint('sylius_refund.at_least_one_unit_should_be_selected_to_refund');
            }

            /** @var string $comment */
            $comment = $request->request->get('sylius_refund_comment', '');

            // here we need to return the new RefundUnits command, with new data
            return new RefundUnits(
                $request->attributes->get('orderNumber'),
                $units,
                $shipments,
                (int) $request->request->get('sylius_refund_payment_method'),
                $comment,
                new \DateTime($request->request->get('sylius_scheduled_at'))
            );
        }
    }

And register the new service:

.. code-block:: yaml

    # config/services.yaml
    Sylius\RefundPlugin\Creator\RefundUnitsCommandCreatorInterface:
        class: App\Creator\RefundUnitsCommandCreator
        arguments:
            - '@Sylius\RefundPlugin\Converter\RefundUnitsConverterInterface'

**5. Modify the ``RefundUnitsHandler``:**

Now, when we have a new command, we also need to overwrite the related command handler:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\CommandHandler;

    use Sylius\Component\Core\Model\OrderInterface;
    use Sylius\Component\Core\Repository\OrderRepositoryInterface;
    use App\Command\RefundUnits;
    use App\Event\UnitsRefunded;
    use Sylius\RefundPlugin\Refunder\RefunderInterface;
    use Sylius\RefundPlugin\Validator\RefundUnitsCommandValidatorInterface;
    use Symfony\Component\Messenger\MessageBusInterface;
    use Webmozart\Assert\Assert;

    final class RefundUnitsHandler
    {
        /** @var RefunderInterface */
        private $orderUnitsRefunder;

        /** @var RefunderInterface */
        private $orderShipmentsRefunder;

        /** @var MessageBusInterface */
        private $eventBus;

        /** @var OrderRepositoryInterface */
        private $orderRepository;

        /** @var RefundUnitsCommandValidatorInterface */
        private $refundUnitsCommandValidator;

        public function __construct(
            RefunderInterface $orderUnitsRefunder,
            RefunderInterface $orderShipmentsRefunder,
            MessageBusInterface $eventBus,
            OrderRepositoryInterface $orderRepository,
            RefundUnitsCommandValidatorInterface $refundUnitsCommandValidator
        ) {
            $this->orderUnitsRefunder = $orderUnitsRefunder;
            $this->orderShipmentsRefunder = $orderShipmentsRefunder;
            $this->eventBus = $eventBus;
            $this->orderRepository = $orderRepository;
            $this->refundUnitsCommandValidator = $refundUnitsCommandValidator;
        }

        public function __invoke(RefundUnits $command): void
        {
            $this->refundUnitsCommandValidator->validate($command);

            $orderNumber = $command->orderNumber();

            /** @var OrderInterface $order */
            $order = $this->orderRepository->findOneByNumber($orderNumber);

            $refundedTotal = 0;
            $refundedTotal += $this->orderUnitsRefunder->refundFromOrder($command->units(), $orderNumber);
            $refundedTotal += $this->orderShipmentsRefunder->refundFromOrder($command->shipments(), $orderNumber);

            /** @var string|null $currencyCode */
            $currencyCode = $order->getCurrencyCode();
            Assert::notNull($currencyCode);

            $this->eventBus->dispatch(new UnitsRefunded(
                $orderNumber,
                $command->units(),
                $command->shipments(),
                $command->paymentMethodId(),
                $refundedTotal,
                $currencyCode,
                $command->comment(),
                $command->getScheduledAt()
            ));
        }
    }

And register it:

.. code-block:: yaml

    # config/services.yaml
    Sylius\RefundPlugin\CommandHandler\RefundUnitsHandler:
        class: App\CommandHandler\RefundUnitsHandler
        arguments:
            - '@Sylius\RefundPlugin\Refunder\OrderItemUnitsRefunder'
            - '@Sylius\RefundPlugin\Refunder\OrderShipmentsRefunder'
            - '@sylius.event_bus'
            - '@sylius.repository.order'
            - '@Sylius\RefundPlugin\Validator\RefundUnitsCommandValidatorInterface'
        tags:
            - { name: messenger.message_handler, bus: sylius.command_bus }

**6. Modify the ``UnitsReturned`` event:**

In previous command handler we are dispatching a new event so now we need to create this event and related event handler:

event:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Event;

    use Sylius\RefundPlugin\Event\UnitsRefunded as BaseUnitsRefunded;

    final class UnitsRefunded extends BaseUnitsRefunded
    {
        /** @var \DateTimeInterface */
        protected $scheduledAt;

        public function __construct(
            string $orderNumber,
            array $units,
            array $shipments,
            int $paymentMethodId,
            int $amount,
            string $currencyCode,
            string $comment,
            \DateTime $scheduledAt
        ) {
            parent::__construct($orderNumber, $units, $shipments, $paymentMethodId, $amount, $currencyCode, $comment);

            $this->scheduledAt = $scheduledAt;
        }

        public function getScheduledAt(): \DateTimeInterface
        {
            return $this->scheduledAt;
        }
    }

And process manager to handle the new event:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\ProcessManager;

    use App\Entity\Refund\RefundPaymentInterface as AppRefundPaymentInterface;
    use Doctrine\ORM\EntityManagerInterface;
    use Sylius\Component\Core\Model\OrderInterface;
    use Sylius\Component\Core\Model\PaymentMethodInterface;
    use Sylius\Component\Core\Repository\OrderRepositoryInterface;
    use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
    use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
    use Sylius\RefundPlugin\Event\RefundPaymentGenerated;
    use Sylius\RefundPlugin\Event\UnitsRefunded;
    use Sylius\RefundPlugin\Factory\RefundPaymentFactoryInterface;
    use Sylius\RefundPlugin\ProcessManager\UnitsRefundedProcessStepInterface;
    use Sylius\RefundPlugin\Provider\RelatedPaymentIdProviderInterface;
    use Sylius\RefundPlugin\StateResolver\OrderFullyRefundedStateResolverInterface;
    use Symfony\Component\Messenger\MessageBusInterface;
    use Webmozart\Assert\Assert;

    final class RefundPaymentProcessManager implements UnitsRefundedProcessStepInterface
    {
        /** @var OrderFullyRefundedStateResolverInterface */
        private $orderFullyRefundedStateResolver;

        /** @var RelatedPaymentIdProviderInterface */
        private $relatedPaymentIdProvider;

        /** @var RefundPaymentFactoryInterface */
        private $refundPaymentFactory;

        /** @var OrderRepositoryInterface */
        private $orderRepository;

        /** @var PaymentMethodRepositoryInterface */
        private $paymentMethodRepository;

        /** @var EntityManagerInterface */
        private $entityManager;

        /** @var MessageBusInterface */
        private $eventBus;

        public function __construct(
            OrderFullyRefundedStateResolverInterface $orderFullyRefundedStateResolver,
            RelatedPaymentIdProviderInterface $relatedPaymentIdProvider,
            RefundPaymentFactoryInterface $refundPaymentFactory,
            OrderRepositoryInterface $orderRepository,
            PaymentMethodRepositoryInterface $paymentMethodRepository,
            EntityManagerInterface $entityManager,
            MessageBusInterface $eventBus
        ) {
            $this->orderFullyRefundedStateResolver = $orderFullyRefundedStateResolver;
            $this->relatedPaymentIdProvider = $relatedPaymentIdProvider;
            $this->refundPaymentFactory = $refundPaymentFactory;
            $this->orderRepository = $orderRepository;
            $this->paymentMethodRepository = $paymentMethodRepository;
            $this->entityManager = $entityManager;
            $this->eventBus = $eventBus;
        }

        public function next(UnitsRefunded $unitsRefunded): void
        {
            /** @var OrderInterface|null $order */
            $order = $this->orderRepository->findOneByNumber($unitsRefunded->orderNumber());
            Assert::notNull($order);

            /** @var PaymentMethodInterface|null $paymentMethod */
            $paymentMethod = $this->paymentMethodRepository->find($unitsRefunded->paymentMethodId());
            Assert::notNull($paymentMethod);

            /** @var AppRefundPaymentInterface $refundPayment */
            $refundPayment = $this->refundPaymentFactory->createWithData(
                $order,
                $unitsRefunded->amount(),
                $unitsRefunded->currencyCode(),
                RefundPaymentInterface::STATE_NEW,
                $paymentMethod
            );
            $refundPayment->setScheduledAt($unitsRefunded->getScheduledAt());

            $this->entityManager->persist($refundPayment);
            $this->entityManager->flush();

            $this->eventBus->dispatch(new RefundPaymentGenerated(
                $refundPayment->getId(),
                $unitsRefunded->orderNumber(),
                $unitsRefunded->amount(),
                $unitsRefunded->currencyCode(),
                $unitsRefunded->paymentMethodId(),
                $this->relatedPaymentIdProvider->getForRefundPayment($refundPayment)
            ));

            $this->orderFullyRefundedStateResolver->resolve($unitsRefunded->orderNumber());
        }
    }

And register it:

.. code-block:: yaml

    Sylius\RefundPlugin\ProcessManager\RefundPaymentProcessManager:
        class: App\ProcessManager\RefundPaymentProcessManager
        arguments:
            - '@Sylius\RefundPlugin\StateResolver\OrderFullyRefundedStateResolverInterface'
            - '@Sylius\RefundPlugin\Provider\RelatedPaymentIdProviderInterface'
            - '@sylius_refund.factory.refund_payment'
            - '@sylius.repository.order'
            - '@sylius.repository.payment_method'
            - '@doctrine.orm.default_entity_manager'
            - '@sylius.event_bus'
        tags:
            - {name: sylius_refund.units_refunded.process_step, priority: 50}

**7. Display the new field on the refund payment:**

And as the last step, we need to overwrite the template ``_refundPayments.html.twig`` from Refund Plugin.
Copy the entire ``_refundPayments.html.twig`` to ``templates/bundles/SyliusRefundPlugin/Order/Admin/_refundPayments.html.twig``:

.. code-block:: bash

    mkdir -p templates/bundles/SyliusRefundPlugin/Order/Admin
    cp vendor/sylius/refund-plugin/src/Resources/views/Order/Admin/_refundPayments.html.twig templates/bundles/SyliusRefundPlugin/Order/Admin/

And replace ``header`` with:

.. code-block:: twig

    <div class="header">
        {{ refund_payment.paymentMethod  }} {%  if refund_payment.scheduledAt is not null %} (Payment should be made in {{ refund_payment.scheduledAt|date('Y-M-d') }}) {% endif %}
    </div>

And that's it, we have a new field on Refund Payment with a "scheduled at" date (when admin/payment gateway
should make the payment), in your application, you probably will add crone to automatize it.
