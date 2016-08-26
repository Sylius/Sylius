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

use Sylius\Component\Resource\Metadata\RegistryInterface;
use Sylius\Bundle\ResourceBundle\Command\DebugResourceCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Sylius\Component\Resource\Metadata\Metadata;

/**
 * @author Daniel Leech <daniel@dantleech.com>
 */
class DebugResourceCommandTest extends \PHPUnit_Framework_TestCase
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
     * It should list all resources if no argument is given.
     */
    public function testListAll()
    {
        $metadata1 = $this->createMetadata('one');
        $metadata2 = $this->createMetadata('two');

        $this->registry->getAll()->willReturn([
            $metadata1->reveal(),
            $metadata2->reveal(),
        ]);
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
     * It should display the metadata for a given resource alias.
     */
    public function testDebugResource()
    {
        $metadata1 = $this->createMetadata('one');

        $this->registry->get('metadata.one')->willReturn($metadata1->reveal());
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
     * @param mixed $suffix
     */
    private function createMetadata($suffix)
    {
        $metadata = $this->prophesize(Metadata::class);
        $metadata->getName()->willReturn($suffix);
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getAlias()->willReturn('sylius.'.$suffix);
        $metadata->getDriver()->willReturn('doctrine/foobar');
        $metadata->getParameters()->willReturn([
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
