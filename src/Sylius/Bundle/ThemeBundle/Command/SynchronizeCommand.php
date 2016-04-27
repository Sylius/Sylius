<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 */
final class SynchronizeCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sylius:theme:synchronize')
            ->setDefinition([
                new InputArgument('theme', InputArgument::OPTIONAL, 'Theme name (e.g. sylius/demo-theme)'),
            ])
            ->setDescription('Synchronize theme(s).')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('Synchronizing theme(s)... ');

        $this->getContainer()->get('sylius.theme.synchronizer')->synchronize(
            $this->findTheme($input->getArgument('theme'))
        );

        $output->writeln('Success!');
    }

    /**
     * @param null|string $themeName
     *
     * @return null|ThemeInterface
     */
    private function findTheme($themeName = null)
    {
        if (null === $themeName) {
            return;
        }

        /** @var ThemeInterface $theme */
        $theme = $this->getThemeRepository()->findOneByName($themeName);
        if (null === $theme) {
            throw new \InvalidArgumentException(sprintf('Could not find theme identified by name "%s"', $themeName));
        }

        return $theme;
    }

    /**
     * @return ThemeRepositoryInterface
     */
    private function getThemeRepository()
    {
        return $this->getContainer()->get('sylius.repository.theme');
    }
}
