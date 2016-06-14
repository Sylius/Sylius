<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Sylius\Bundle\CoreBundle\Fixture\ChannelFixture;
use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ChannelFixtureTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function it_requires_to_define_at_least_one_channel()
    {
        $this->assertPartialConfigurationIsInvalid([[]], 'channels');
        $this->assertPartialConfigurationIsInvalid([['channels' => []]], 'channels');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new ChannelFixture(
            $this->getMockBuilder(ChannelFactoryInterface::class)->getMock(),
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock()
        );
    }
}
