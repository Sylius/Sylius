<?php

namespace Sylius\Bundle\ThemeBundle\Command;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class DebugThemeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sylius:debug:theme')
            ->setDescription('Shows list of detected themes.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->showSuccessfullyLoadedThemes($output);
    }

    private function showSuccessfullyLoadedThemes(OutputInterface $output)
    {
        /** @var ThemeInterface[] $themes */
        $themes = $this->getContainer()->get('sylius.repository.theme')->findAll();

        if (empty($themes)) {
            $output->writeln("<error>There are no themes.</error>");
            return;
        }

        $output->writeln("<question>Succesfully loaded themes:</question>");

        $table = new Table($output);
        $table->setHeaders(['Name', 'Logical name', 'Path']);

        foreach ($themes as $theme) {
            $table->addRow([$theme->getName(), $theme->getLogicalName(), $theme->getPath()]);
        }

        $table->setStyle("borderless");
        $table->render();
    }
}