<?php

namespace Sylius\Bundle\ThemeBundle\Command;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
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

        if (0 < count($themes)) {
            $output->writeln("<question>Succesfully loaded themes:</question>");

            $maxName = 4;
            $maxLogicalName = 12;
            $maxPath = 4;

            foreach ($themes as $theme) {
                $maxName = max($maxName, strlen($theme->getName()));
                $maxLogicalName = max($maxLogicalName, strlen($theme->getLogicalName()));
                $maxPath = max($maxPath, strlen($theme->getPath()));
            }

            $format = "%-{$maxName}s  %-{$maxLogicalName}s  %-{$maxPath}s";
            $formatHeader = "%-" . ($maxName + 19) . "s  %-" . ($maxLogicalName + 19) . "s  %s";

            $output->writeln(sprintf($formatHeader, "<comment>Name</comment>", "<comment>Logical name</comment>", "<comment>Path</comment>"));

            foreach ($themes as $theme) {
                $output->writeln(sprintf($format, $theme->getName(), $theme->getLogicalName(), $theme->getPath()));
            }
        }
    }
}