<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Tests\DependencyInjection;

use Sylius\Bundle\InventoryBundle\DependencyInjection\SyliusInventoryExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

/**
 * Dependency injection extension test.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusInventoryExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testLoadThrowsExceptionUnlessDriverSet()
    {
        $extension = new SyliusInventoryExtension();
        $config = $this->getEmptyConfig();
        unset($config['driver']);
        $extension->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLoadThrowsExceptionUnlessDriverIsValid()
    {
        $extension = new SyliusInventoryExtension();
        $config = $this->getEmptyConfig();
        $config['driver'] = 'foo';
        $extension->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testLoadThrowsExceptionUnlessInventoryUnitModelClassSet()
    {
        $extension = new SyliusInventoryExtension();
        $config = $this->getEmptyConfig();
        unset($config['classes']['model']['iu']);
        $extension->load(array($config), new ContainerBuilder());
    }

    /**
     * Get empty config for testing.
     *
     * @return array
     */
    protected function getEmptyConfig()
    {
        $yaml =
<<<EOF
driver: ORM
classes:
    model:
        iu: Sylius\Bundle\InventoryBundle\Model\InventoryUnit
EOF;

        $parser = new Parser();

        return $parser->parse($yaml);
    }
}
