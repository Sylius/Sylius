<?php

namespace Sylius\Bundle\OrderBundle\Command;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository;
use Sylius\Component\Core\Model\Order;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ShowOrderCommand extends Command
{
    const ARG_NUMBER = 'number';

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

        $this->renderOrder($style, $order);

        return self::FAILURE;
    }

    private function renderOrder(SymfonyStyle $style, Order $order): void
    {
        $style->title(sprintf('Order #%s', $order->getNumber()));
    }

}
