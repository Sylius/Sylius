<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\FixturesBundle\Tests\Listener;

use Doctrine\Common\Persistence\ManagerRegistry;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\FixturesBundle\Listener\PHPCRPurgerListener;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class PHPCRPurgerListenerTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function managers_are_set_to_null_by_default(): void
    {
        $this->assertProcessedConfigurationEquals([[]], ['managers' => [null]], 'managers');
    }

    /**
     * @test
     */
    public function managers_are_optional(): void
    {
        $this->assertProcessedConfigurationEquals([['managers' => ['custom']]], ['managers' => ['custom']], 'managers');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration(): ConfigurationInterface
    {
        return new PHPCRPurgerListener($this->getMockBuilder(ManagerRegistry::class)->getMock());
    }
}
