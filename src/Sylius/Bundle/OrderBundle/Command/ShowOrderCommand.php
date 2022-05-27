<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\OrderBundle\Command;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Traversable;

class ShowOrderCommand extends Command
{
    public const ARG_NUMBER = 'number';

    public const DATE_FORMAT = 'Y-m-d H:i:s';

    protected static $defaultName = 'sylius:order:show';

    private OrderRepository $orderRepository;

    protected function configure(): void
    {
        $this->addArgument(self::ARG_NUMBER, InputArgument::REQUIRED, 'Order number');
    }

    public function __construct(OrderRepository $orderRepository)
    {
        parent::__construct();
        $this->orderRepository = $orderRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $number = (string) $input->getArgument(self::ARG_NUMBER);

        // do not use findOneByNumber as we want also to get cart orders
        $order = $this->orderRepository->findOneBy(['number' => $number]);
        $style = new SymfonyStyle($input, $output);
        if (null === $order) {
            $style->error(sprintf('Order with number "%s" not found', $number));

            return self::FAILURE;
        }

        $this->renderOrder($style, $output, $order);

        return self::FAILURE;
    }

    private function renderOrder(SymfonyStyle $style, OutputInterface $output, Order $order): void
    {
        $style->title(sprintf('Order #%s', $order->getNumber()));

        $style->section('Attributes');
        $this->renderAttributes($output, $order);
        $style->section('Items');
        $this->renderItems($output, $order);
        $style->section('Payments');
        $this->renderPayments($output, $order->getPayments());
    }

    private function renderAttributes(OutputInterface $output, Order $order)
    {
        $this->fieldValueTable($output, [
            [
                ['Created', $order->getCreatedAt()->format(self::DATE_FORMAT)],
                ['Updated', $order->getUpdatedAt()->format(self::DATE_FORMAT)],
                ['Completed', $order->getCheckoutCompletedAt()->format(self::DATE_FORMAT)],
            ],
            [
                ['Id', $order->getId()],
                ['Token', $order->getTokenValue(), 2],
            ],
        ]);
        $this->fieldValueTable($output, [
            [
                ['Customer', sprintf('%s (id: %s)', $order->getCustomer()?->getEmail(), $order->getCustomer()?->getId())],
                ['Billing Addr.', $order->getBillingAddress()?->getId()],
                ['Shipping Addr.', $order->getShippingAddress()?->getId()],
            ],
        ]);

        $this->fieldValueTable($output, [
            [
                ['State', $order->getState()],
                ['Checkout', $order->getCheckoutState()],
                ['Shipping', $order->getShippingState()],
                ['Payment', $order->getPaymentState()],
            ],
            [
                ['Channel', $order->getChannel()?->getCode()],
                ['Locale', $order->getLocaleCode()],
                ['Currency', $order->getCurrencyCode()],
                ['Guest', $order->getCreatedByGuest() ? '✔' : '✘'],
            ],
        ]);
    }

    private function fieldValueTable(OutputInterface $output, array $fieldValues): void
    {
        $table = new Table($output);
        $table->setRows(array_map(
            fn (array $row) => array_reduce(
                $row,
                fn (array $carry, array $pair) => array_merge($carry, $this->fieldValue($pair[0], $pair[1], $pair[2] ?? 1)),
                [],
            ),
            $fieldValues
        ));
        $table->setStyle('compact');
        $table->render();
        $output->writeln('');
    }

    /**
     * @return array{string,TableCell}
     */
    private function fieldValue(string $field, mixed $value, int $span = 1): array
    {
        return [
            sprintf('<fg=#aaa>%s:</>', $field),
            new TableCell((string)($value ?? 'n/a'), [
                'colspan' => $span,
            ]),
        ];
    }

    private function renderCustomer(OutputInterface $output, ?CustomerInterface $customer): void
    {
        if (null === $customer) {
            return;
        }

        $this->fieldValueTable($output, [
            [
                ['id', $customer->getId()],
                ['email', $customer->getEmail()],
                ['group', $customer->getGroup()?->getCode()],
            ],
        ]);
    }

    private function renderAddress(OutputInterface $output, ?AddressInterface $address): void
    {
        if (null === $address) {
            $output->writeln('No address');
            $output->writeln('');

            return;
        }

        $this->fieldValueTable($output, [
            [
                ['id', $address->getId()],
                ['First Name', $address->getFirstName()],
                ['Last Name', $address->getLastName()],
                ['Company', $address->getCompany()],
                ['Company', $address->getCompany()],
            ],
            [
                ['Street', $address->getId()],
                ['City', $address->getFirstName()],
                ['Postcode', $address->getLastName()],
                ['Province', sprintf('%s (%s)', $address->getProvinceName(), $address->getProvinceCode())],
            ],
        ]);
    }

    private function renderItems(OutputInterface $output, Order $order): void
    {
        $table = new Table($output);
        $table->setHeaders([
            'ID',
            'Variant',
            'Unit price',
            'Quantity',
            'Total',
        ]);
        /** @var OrderItemInterface $item */
        foreach ($order->getItems() as $item) {
            $table->addRow([
                $item->getId(),
                sprintf('%s (id:%s)', $item->getVariantName(), $item->getVariant()?->getId()),
                $item->getUnitPrice(),
                $item->getQuantity(),
                $item->getTotal(),
            ]);
        }
        $table->setStyle('default');
        $table->addRow(new TableSeparator());
        $table->addRows(array_map(
            fn (array $pair) => [
                new TableCell(
                    $pair[0],
                    ['colspan' => 4, 'style' => new TableCellStyle(['align' => 'right'])]
                ),
                $pair[1],
            ],
            [
                ['Shipping', $order->getShippingTotal()],
                ['Adjustments', $order->getAdjustmentsTotal()],
                ['Total', $order->getTotal()],
            ],
        ));
        $table->render();
        $output->writeln('');
    }

    /**
     * @param Traversable<PaymentInterface> $payments
     */
    private function renderPayments(OutputInterface $output, Traversable $payments): void
    {
        $table = new Table($output);
        $table->setHeaders([
            'ID',
            'Method',
            'State',
            'Created',
            'Updated',
            'Amount',
            'Currency',
        ]);
        foreach ($payments as $payment) {
            $table->addRow([
                $payment->getId(),
                $payment->getMethod()?->getName() ?? 'n/a',
                $payment->getState(),
                $payment->getCreatedAt()?->format(self::DATE_FORMAT),
                $payment->getUpdatedAt()?->format(self::DATE_FORMAT),
                $payment->getAmount(),
                $payment->getCurrencyCode(),
            ]);
        }
        $table->render();
        $output->writeln('');
    }
}
