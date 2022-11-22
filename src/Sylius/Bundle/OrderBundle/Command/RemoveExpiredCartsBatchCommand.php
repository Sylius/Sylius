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

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\OrderBundle\SyliusExpiredCartsEvents;
use Sylius\Component\Order\Model\OrderInterface;
use SyliusLabs\Polyfill\Symfony\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class RemoveExpiredCartsBatchCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'sylius:remove-expired-carts-batch';

    protected static $defaultDescription = 'Removes carts that have been idle for a period set in `sylius_order.expiration.cart` configuration key.';

    public function __construct(
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $eventDispatcher,
        private string $orderClass,
        private string $expirationTime
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'batch-size',
                InputArgument::OPTIONAL,
                'How many carts will be removed at once',
                '100'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $batchSize = (int)$input->getArgument('batch-size');
        $io = new SymfonyStyle($input, $output);
        $io->title(
            sprintf('Command will remove carts that have been idle for <info>%s</info>.', $this->expirationTime)
        );

        $query = $this->entityManager->createQueryBuilder()
            ->from($this->orderClass, 'o')
            ->select('o')
            ->andWhere('o.state = :state')
            ->andWhere('o.updatedAt < :terminalDate')
            ->setParameter('state', OrderInterface::STATE_CART)
            ->setParameter('terminalDate', new \DateTime('-'.$this->expirationTime))
            ->getQuery();

        $io->info(sprintf('Removing carts in batches of %d...', $batchSize));
        $i = 0;
        $expiredCarts = [];
        foreach ($io->progressIterate($query->toIterable()) as $cart) {
            $this->entityManager->remove($cart);
            $expiredCarts[] = $cart;
            ++$i;
            if (($i % $batchSize) === 0) {
                $this->executeDeletions($expiredCarts);
                $expiredCarts = [];
            }
        }

        $this->executeDeletions($expiredCarts);

        $io->success(sprintf('Successfully removed %d expired cards!', $i));

        return self::SUCCESS;
    }

    private function executeDeletions(array $expiredCarts): void
    {
        $this->eventDispatcher->dispatch(new GenericEvent($expiredCarts), SyliusExpiredCartsEvents::PRE_REMOVE);
        $this->entityManager->flush(); // Executes all deletions.
        $this->eventDispatcher->dispatch(new GenericEvent($expiredCarts), SyliusExpiredCartsEvents::POST_REMOVE);
        $this->entityManager->clear(); // Detaches all objects from Doctrine!
    }
}
