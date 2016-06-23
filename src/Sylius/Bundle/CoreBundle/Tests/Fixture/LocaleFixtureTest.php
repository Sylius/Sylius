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
use Sylius\Bundle\CoreBundle\Fixture\LocaleFixture;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class LocaleFixtureTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function locales_are_not_required()
    {
        $this->assertConfigurationIsValid([[]], 'locales');
    }

    /**
     * @test
     */
    public function locales_can_be_set()
    {
        $this->assertConfigurationIsValid([['locales' => ['en_US' => true, 'pl_PL' => false, 'es_ES' => true]]], 'locales');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new LocaleFixture(
            $this->getMockBuilder(FactoryInterface::class)->getMock(),
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            'default_LOCALE'
        );
    }
}
