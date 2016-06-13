<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\Tests\Listener;

use Doctrine\Common\Persistence\ManagerRegistry;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Sylius\Bundle\FixturesBundle\Listener\ORMPurgerListener;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ORMPurgerListenerTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    function its_purge_mode_is_delete()
    {
        $this->assertProcessedConfigurationEquals([[]], ['mode' => 'delete'], 'mode');
    }

    /**
     * @test
     */
    function its_purge_mode_can_be_changed_to_truncate()
    {
        $this->assertProcessedConfigurationEquals([['mode' => 'truncate']], ['mode' => 'truncate'], 'mode');
    }

    /**
     * @test
     */
    function its_purge_mode_cannot_be_changed_to_anything_else_than_defined()
    {
        $this->assertPartialConfigurationIsInvalid([['mode' => 'lol']], 'mode');
    }

    /**
     * @test
     */
    function its_manager_is_the_default_one()
    {
        $this->assertProcessedConfigurationEquals([[]], ['managers' => [null]], 'managers');
    }

    /**
     * @test
     */
    function its_default_manager_can_be_replaced_with_custom_ones()
    {
        $this->assertProcessedConfigurationEquals([['managers' => ['custom']]], ['managers' => ['custom']], 'managers');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new ORMPurgerListener($this->getMockBuilder(ManagerRegistry::class)->getMock());
    }
}
