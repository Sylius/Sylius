<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Client;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeBundleTestCase extends WebTestCase
{
    const TEST_CASE = 'DefaultTestCase';

    protected function setUp()
    {
        parent::setUp();

        $this->deleteTmpDir(self::TEST_CASE);
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->deleteTmpDir(self::TEST_CASE);
    }

    /**
     * @return Client
     */
    protected function getClient()
    {
        $client = $this->createClient(['test_case' => self::TEST_CASE, 'root_config' => 'config.yml']);
        try {
            $client->insulate();
        } catch (\RuntimeException $e) {
            // Don't insulate requests if not possible to do so.
        }

        return $client;
    }
}
