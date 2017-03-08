<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Tests\Command;

use Sylius\Bundle\ResourceBundle\Command\DebugResourceCommand;
use Sylius\Component\Resource\Metadata\Metadata;
use Sylius\Component\Resource\Metadata\RegistryInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Daniel Leech <daniel@dantleech.com>
 */
final class DebugResourceCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    /**
     * @var CommandTester
     */
    private $tester;

    public function setUp()
    {
        $this->registry = $this->prophesize(RegistryInterface::class);

        $command = new DebugResourceCommand($this->registry->reveal());
        $this->tester = new CommandTester($command);
    }

    /**
     * @test
     */
    public function it_lists_all_resources_if_no_argument_is_given()
    {
        $this->registry->getAll()->willReturn([$this->createMetadata('one'), $this->createMetadata('two')]);
        $this->tester->execute([]);
        $display = $this->tester->getDisplay();

        $this->assertEquals(<<<'EOT'
+------------+
| Alias      |
+------------+
| sylius.one |
| sylius.two |
+------------+

EOT
        , $display);
    }

    /**
     * @test
     */
    public function it_displays_the_metadata_for_given_resource_alias()
    {
        $this->registry->get('metadata.one')->willReturn($this->createMetadata('one'));
        $this->tester->execute([
            'resource' => 'metadata.one',
        ]);

        $display = $this->tester->getDisplay();

        $this->assertEquals(<<<'EOT'
+------------------------------+-----------------+
| name                         | one             |
| application                  | sylius          |
| driver                       | doctrine/foobar |
| classes.foo                  | bar             |
| classes.bar                  | foo             |
| whatever.something.elephants | camels          |
+------------------------------+-----------------+

EOT
        , $display);
    }

    /**
     * @param string $suffix
     *
     * @return Metadata
     */
    private function createMetadata($suffix)
    {
        $metadata = Metadata::fromAliasAndConfiguration(sprintf('sylius.%s', $suffix), [
            'driver' => 'doctrine/foobar',
            'classes' => [
                'foo' => 'bar',
                'bar' => 'foo',
            ],
            'whatever' => [
                'something' => [
                    'elephants' => 'camels',
                ],
            ],
        ]);

        return $metadata;
    }
}
