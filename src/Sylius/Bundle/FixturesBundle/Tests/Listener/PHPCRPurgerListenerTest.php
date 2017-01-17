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
use Sylius\Bundle\FixturesBundle\Listener\PHPCRPurgerListener;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class PHPCRPurgerListenerTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function managers_are_set_to_null_by_default()
    {
        $this->assertProcessedConfigurationEquals([[]], ['managers' => [null]], 'managers');
    }

    /**
     * @test
     */
    public function managers_are_optional()
    {
        $this->assertProcessedConfigurationEquals([['managers' => ['custom']]], ['managers' => ['custom']], 'managers');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new PHPCRPurgerListener($this->getMockBuilder(ManagerRegistry::class)->getMock());
    }
}
