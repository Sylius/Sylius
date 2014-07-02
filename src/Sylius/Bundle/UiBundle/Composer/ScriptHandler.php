<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UiBundle\Composer;

use Symfony\Component\ClassLoader\ClassCollectionLoader;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;
use Composer\Script\CommandEvent;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class ScriptHandler
{
    /**
     * Install assets via bower.
     *
     * @param $event CommandEvent A instance
     */
    public static function runBower(CommandEvent $event)
    {
        static::executeCommand($event, sprintf('cd %s && %s install', $bundePath, $bowerPath), $options['process-timeout']);
    }

    protected static function executeCommand(CommandEvent $event, $command, $timeout = 300)
    {
        $process = new Process($command, null, null, null, $timeout);
        $process->run(function ($type, $buffer) { echo $buffer; });

        if (!$process->isSuccessful()) {
            throw new \RuntimeException(sprintf('An error occurred when executing the "%s" command.', escapeshellarg($command)));
        }
    }

    protected static function getOptions(CommandEvent $event)
    {
        $options = array_merge(array(
            'symfony-app-dir' => 'app',
        ), $event->getComposer()->getPackage()->getExtra());

        $options['process-timeout'] = $event->getComposer()->getConfig()->get('process-timeout');

        return $options;
    }
}
