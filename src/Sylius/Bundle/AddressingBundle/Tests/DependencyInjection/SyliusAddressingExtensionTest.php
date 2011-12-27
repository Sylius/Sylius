<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Sylius\Bundle\AddressingBundle\DependencyInjection\SyliusAddressingExtension;
use Symfony\Component\Yaml\Parser;

class SyliusAddressingExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessDriverSet()
    {
        $loader = new SyliusAddressingExtension();
        $config = $this->getEmptyConfig();
        unset($config['driver']);
        $loader->load(array($config), new ContainerBuilder());
    }
    
    /**
    * @expectedException \InvalidArgumentException
    */
    public function testUserLoadThrowsExceptionUnlessDriverIsValid()
    {
        $loader = new SyliusAddressingExtension();
        $config = $this->getEmptyConfig();
        $config['driver'] = 'foo';
        $loader->load(array($config), new ContainerBuilder());
    }
    
    /**
    * @expectedException \InvalidArgumentException
    */
    public function testUserLoadThrowsExceptionUnlessEngineIsValid()
    {
        $loader = new SyliusAddressingExtension();
        $config = $this->getEmptyConfig();
        $config['engine'] = 'foo';
        $loader->load(array($config), new ContainerBuilder());
    }
    
    /**
    * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
    */
    public function testUserLoadThrowsExceptionUnlessaddressModelClassSet()
    {
        $loader = new SyliusAddressingExtension();
        $config = $this->getEmptyConfig();
        unset($config['classes']['model']['address']);
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * getEmptyConfig
     *
     * @return array
     */
    protected function getEmptyConfig()
    {
        $yaml = <<<EOF
driver: ORM
classes:
    model:
        address: Sylius\Bundle\AddressingBundle\Entity\DefaultAddress
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }
}