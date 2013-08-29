<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Tests\DependencyInjection;

use Sylius\Bundle\FlowBundle\DependencyInjection\SyliusFlowExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

/**
 * Dependency injection extension test.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusFlowExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function shouldThrowExceptionUnlessStorageConfigured()
    {
        $extension = new SyliusFlowExtension();

        $config = $this->getEmptyConfig();
        $config['storage'] = '';

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
storage: sylius.process_storage.session
EOF;

        $parser = new Parser();

        return $parser->parse($yaml);
    }
}
