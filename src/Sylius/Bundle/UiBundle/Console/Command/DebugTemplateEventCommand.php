<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\UiBundle\Console\Command;

use Sylius\Bundle\UiBundle\Registry\TemplateBlock;
use Sylius\Bundle\UiBundle\Registry\TemplateBlockRegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class DebugTemplateEventCommand extends Command
{
    protected static $defaultName = 'sylius:debug:template-event';

    public function __construct(private TemplateBlockRegistryInterface $templateBlockRegistry)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Debug template events and associated blocks')
            ->addArgument('event', InputArgument::OPTIONAL, 'Template event name', null)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $eventName = $input->getArgument('event');

        if ($eventName === null) {
            $io->title('List of template events');

            $io->listing(array_keys($this->templateBlockRegistry->all()));

            return 0;
        }

        $io->title(sprintf('Blocks registered for the template event "%s"', $eventName));

        $io->table(
            ['Block name', 'Template', 'Priority', 'Enabled'],
            array_map(
                static fn (TemplateBlock $templateBlock): array => [
                    $templateBlock->getName(),
                    $templateBlock->getTemplate(),
                    $templateBlock->getPriority(),
                    $templateBlock->isEnabled() ? 'TRUE' : 'FALSE',
                ],
                $this->templateBlockRegistry->all()[$eventName] ?? [],
            ),
        );

        return 0;
    }
}

class_alias(DebugTemplateEventCommand::class, \Sylius\Bundle\UiBundle\Command\DebugTemplateEventCommand::class);
