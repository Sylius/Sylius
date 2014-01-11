<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\InstallerBundle\Persister;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Yaml\Yaml;

class YamlPersisterSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith($this->getTmpFileName());
    }

    function it_dumps_configuration()
    {
        $this->dump(array('database' => array('user' => 'root')));

        if (Yaml::dump(array('parameters' => array('user' => 'root'))) !== $actial = file_get_contents($this->getTmpFileName())) {
            throw new \UnexpectedValueException($actial);
        }
    }

    private function getTmpFileName()
    {
        return sys_get_temp_dir().DIRECTORY_SEPARATOR.'parameters.yml';
    }

    function letgo()
    {
        unlink($this->getTmpFileName());
    }
}
