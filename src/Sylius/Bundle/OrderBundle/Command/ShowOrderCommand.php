<?php

namespace Sylius\Bundle\OrderBundle\Command;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository;
use Sylius\Component\Core\Model\Order;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ShowOrderCommand extends Command
{
    const ARG_NUMBER = 'number';
    const DATE_FORMAT = 'Y-m-d H:i:s';


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
        $number = (string)$input->getArgument(self::ARG_NUMBER);

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

        $this->fieldValueTable($output, [
            [
                ['Created', $order->getCreatedAt()->format(self::DATE_FORMAT)],
                ['Updated', $order->getUpdatedAt()->format(self::DATE_FORMAT)],
                ['Completed', $order->getCheckoutCompletedAt()->format(self::DATE_FORMAT)],
            ],
            [
                ['Token', $order->getTokenValue(), 3],
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
                ['Channel', $order?->getChannel()->getCode()],
                ['Locale', $order->getLocaleCode()],
                ['Currency', $order->getCurrencyCode()],
                ['Guest', $order->getCreatedByGuest() ? '✔' : '✘'],
            ],
        ]);
        $this->fieldValueTable($output, [
            [
            ],
        ]);
    }

    private function fieldValueTable(OutputInterface $output, array $fieldValues): void
    {
        $table = new Table($output);
        $table->setRows(array_map(
            fn (array $row) => array_reduce(
                $row,
                fn (array $carry, array $pair) => [...$carry, ...$this->fieldValue($pair[0], $pair[1], $pair[2] ?? 1)],
                [],
            ),
            $fieldValues
        ));
        $table->setStyle('compact');
        $table->render();
        $output->writeln('');
    }

    /**
     * @return array{string,string}
     */
    private function fieldValue(string $field, ?string $value, int $span): array
    {
        return [
            sprintf('<fg=#aaa>%s:</>', $field),
            new TableCell($value ?? 'n/a', [
                'colspan' => $span,
            ]),
        ];
    }


}
