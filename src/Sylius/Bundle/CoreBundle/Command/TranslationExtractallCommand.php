<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Finder\Finder;

/**
 * Command to extract new translations from all bundles
 *
 * @author Plamen Petrov <paceto256@gmail.com>
 */
class TranslationExtractallCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
                ->setName('sylius:translation:extractall')
                ->addArgument('locales', InputArgument::IS_ARRAY, 'The locales for which to extract messages.')
                ->setDescription('Extracts translations for all bundles for specific locale')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $rootDir = $this->getContainer()->get('kernel')->getRootDir();
        $locales = $input->getArgument('locales');

        if (empty($locales)) {
            throw new \InvalidArgumentException('No locale was given.');
        }
        
        $translationExtractCommand = $this->getApplication()->find('translation:extract');

        $finder = new Finder();
        $finder->depth('== 0')->directories()->in($rootDir . '/../src/Sylius/Bundle/');

        foreach ($finder as $file) {
            $rowBundle = $file->getRelativePathname();
                    
            $input = new ArrayInput(array(
                'locales' => $locales,
                '--dir' => array( $rootDir . '/../src/Sylius/Bundle/' . $rowBundle . '/'),
                '--output-dir' => $rootDir . '/../src/Sylius/Bundle/' . $rowBundle . '/Resources/translations/',
                '--output-format' => 'yml'
            ));

            try {
                $returnCode = $translationExtractCommand->run($input, $output);
            } catch(\Exception $e) {
                echo $e->getMessage();
            }

            if ($returnCode != 0) {
                $output = json_decode(rtrim($output));
            }
        }

        $output->writeln('Extracting translations ...');
        $output->writeln('All new translations has been extracted.');
    }

}
