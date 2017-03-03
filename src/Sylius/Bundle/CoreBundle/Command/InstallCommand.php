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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\RuntimeException;

final class InstallCommand extends AbstractInstallCommand
{
    /**
     * @var array
     */
    private $commands = [
        [
            'command' => 'check-requirements',
            'message' => 'Checking system requirements.',
        ],
        [
            'command' => 'database',
            'message' => 'Setting up the database.',
        ],
        [
            'command' => 'setup',
            'message' => 'Shop configuration.',
        ],
        [
            'command' => 'assets',
            'message' => 'Installing assets.',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sylius:install')
            ->setDescription('Installs Sylius in your preferred environment.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command installs Sylius.
EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln('<info>Installing Sylius...</info>');
        $outputStyle->writeln($this->getSyliusLogo());

        $this->ensureDirectoryExistsAndIsWritable($this->getContainer()->getParameter('kernel.cache_dir'), $output);

        $errored = false;
        foreach ($this->commands as $step => $command) {
            try {
                $outputStyle->newLine();
                $outputStyle->section(sprintf(
                    'Step %d of %d. <info>%s</info>',
                    $step + 1,
                    count($this->commands),
                    $command['message']
                ));
                $this->commandExecutor->runCommand('sylius:install:'.$command['command'], [], $output);
            } catch (RuntimeException $exception) {
                $errored = true;
            }
        }

        $frontControllerPath = 'prod' === $this->getEnvironment() ? '/' : sprintf('/app_%s.php', $this->getEnvironment());

        $outputStyle->newLine(2);
        $outputStyle->success($this->getProperFinalMessage($errored));
        $outputStyle->writeln(sprintf(
            'You can now open your store at the following path under the website root: <info>%s.</info>',
            $frontControllerPath
        ));
    }

    /**
     * @param bool $errored
     *
     * @return string
     */
    private function getProperFinalMessage($errored)
    {
        if ($errored) {
            return 'Sylius has been installed, but some error occurred.';
        }

        return 'Sylius has been successfully installed.';
    }

    /**
     * @return string
     */
    private function getSyliusLogo()
    {
        return '                                                                  
           <info>,</info>                                                       
         <info>,;:,</info>                                                      
       <info>`;;;.:`</info>                                                     
      <info>`::;`  :`</info>                                                    
       <info>:::`   `</info>          .\'++:           \'\'.   \'.                  
       <info>`:::</info>             :+\',;+\'          :+;  `+.                  
        <info>::::</info>            +\'   :\'          `+;                       
        <info>`:::,</info>           \'+`     ++    :+.`+; `++. ;+\'    \'\'  ,++++.
         <info>,:::`</info>          `++\'.   .+:  `+\' `+;  .+,  ;+    +\'  +;  \'\'
          <info>::::`</info>           ,+++.  \'+` :+. `+;  `+,  ;+    +\'  \'+.   
   <info>,.     .::::</info>             .++` `+: +\'  `+;  `+,  ;+    +\'  `;++; 
<info>`;;.:::`   :::::</info>             :+.  \'+,+.  `+;  `+,  ;+   `+\'     .++
 <info>.;;;;;;::`.::::,</info>       +\'` `++   `++\'   `+;  `+:  :+. `++\'  \'.  ;+
  <info>,;;;;;;;;;:::::</info>       .+++++`    ;+,    ++;  ++, `\'+++,\'+\' :++++,
   <info>,;;;;;;;;;:::</info>`                  ;\'                              
    <info>:;;;;;;;;;:,</info>                :.:+,                              
     <info>;;;;;;;;;:</info>                 ;++,'
        ;
    }
}
