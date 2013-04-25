<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Composer;

use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler as BaseScriptHandler;

class ScriptHandler extends BaseScriptHandler
{
    public static function installParametersFile($event)
    {
        $options = self::getOptions($event);
        $appDir = $options['symfony-app-dir'];

        if (!is_dir($appDir)) {
            echo sprintf('The symfony-app-dir (%s) specified in composer.json was not found in %s, can not install the parameters.yml file.', $appDir, getcwd()).PHP_EOL;

            return;
        }

        if (is_file($appDir.'/config/container/parameters.yml.dist') && !is_file($appDir.'/config/container/parameters.yml')) {
            copy($appDir.'/config/container/parameters.yml.dist', $appDir.'/config/container/parameters.yml');
        }
    }
}
