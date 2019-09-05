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

namespace Sylius\Bundle\CoreBundle\Command;

use \Sylius\Bundle\CoreBundle\Command\Order\CancelUnpaidOrdersCommand as NewCancelUnpaidOrdersCommand;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

@trigger_error(sprintf('The "%s" class is deprecated since Sylius 1.4, use "%s" instead.', CancelUnpaidOrdersCommand::class, NewCancelUnpaidOrdersCommand::class), E_USER_DEPRECATED);

class CancelUnpaidOrdersCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('sylius:cancel-unpaid-orders')
            ->setDescription(
                'Removes order that have been unpaid for a configured period. Configuration parameter - sylius_order.order_expiration_period.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        /** @var NewCancelUnpaidOrdersCommand $command */
        $command = $this->getContainer()->get('Sylius\Bundle\CoreBundle\Command\Order\CancelUnpaidOrdersCommand');
        $command->run($input, $output);
    }
}
