<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ApiBundle\Test;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use h4cc\AliceFixturesBundle\Fixtures\FixtureSet;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Coduo\PHPMatcher\Factory\SimpleFactory;

/**
 * @author William Durand <william.durand1@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
abstract class ApiTestCase extends BaseWebTestCase
{
    public function setUp()
    {
        $purger = new ORMPurger($this->get('doctrine.orm.entity_manager'));
        $purger->purge();
    }

    protected function assertJsonResponse($response, $statusCode = 200)
    {
        $this->assertEquals(
            $statusCode, $response->getStatusCode(),
            $response->getContent()
        );

        $this->assertTrue(
            $response->headers->contains('Content-Type', 'application/json'),
            $response->headers
        );
    }

    protected function assertJsonResponseContent($response, $filename)
    {
        $expectedResponse = file_get_contents(__DIR__.sprintf('/../Tests/Responses/%s.json', $filename));
        $actualResponse = $response->getContent();

        $actualResponse = json_encode(json_decode($actualResponse), JSON_PRETTY_PRINT);

        $factory = new SimpleFactory();
        $matcher = $factory->createMatcher();

        $result = $matcher->match($actualResponse, $expectedResponse);

        if (!$result) {
            echo $matcher->getError();

            $expectedResponse = explode(PHP_EOL, (string) $expectedResponse);
            $actualResponse   = explode(PHP_EOL, (string) $actualResponse);

            $diff = new \Diff($expectedResponse, $actualResponse, array());

            $renderer = new \Diff_Renderer_Text_Unified;
            echo $diff->render($renderer);
        }

        $this->assertTrue($result);
    }

    protected function loadFixtures($filename)
    {
        $manager = $this->get('h4cc_alice_fixtures.manager');

        $fixtureSet = new FixtureSet();
        $fixtureSet->addFile(__DIR__.sprintf('/../Tests/Fixtures/%s.yml', $filename), 'yaml');

        $manager->load($fixtureSet);
    }

    protected function getLastResource($name)
    {
        $resources = $this->get('sylius.repository.'.$name)->findAll();

        return array_pop($resources);
    }

    protected function get($id)
    {
        return static::createClient()->getContainer()->get($id);
    }
}
