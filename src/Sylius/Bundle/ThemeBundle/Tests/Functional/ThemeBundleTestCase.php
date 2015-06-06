<?php

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
        $client = $this->createClient(array('test_case' => self::TEST_CASE, 'root_config' => 'config.yml'));
        try {
            $client->insulate();
        } catch (\RuntimeException $e) {
            // Don't insulate requests if not possible to do so.
        }

        return $client;
    }
}