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

use Coduo\PHPMatcher\Factory\SimpleFactory;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\Finder\Finder;

/**
 * @author William Durand <william.durand1@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
abstract class ApiTestCase extends BaseWebTestCase
{
    private $loader;

    public function setUp()
    {
        $bundle = $this->get('kernel')->getBundle('SyliusFixturesBundle');

        $this->loader = new ContainerAwareLoader(static::createClient()->getContainer());
        $this->loader->loadFromDirectory($bundle->getPath().'/DataFixtures/ORM');

        $executor = new ORMExecutor($this->get('doctrine.orm.entity_manager'), new ORMPurger());
        $executor->execute($this->loader->getFixtures(), true);
    }

    public function tearDown()
    {
        $purger = new ORMPurger($this->get('doctrine.orm.entity_manager'));
        $purger->purge();

        unset($this->loader);
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

            $renderer = new \Diff_Renderer_Text_Unified();
            echo $diff->render($renderer);
        }

        $this->assertTrue($result);
    }

    protected function getLastResource($name)
    {
        $resources = $this->get('sylius.repository.'.$name)->findBy(array(), array('id' => 'DESC'), 1);

        return current($resources);
    }

    protected function get($id)
    {
        return static::createClient()->getContainer()->get($id);
    }

    protected static function getKernelClass()
    {
        if (isset($_SERVER['KERNEL_DIR'])) {
            $dir = $_SERVER['KERNEL_DIR'];

            if (!is_dir($dir)) {
                $phpUnitDir = static::getPhpUnitXmlDir();
                if (is_dir("$phpUnitDir/$dir")) {
                    $dir = "$phpUnitDir/$dir";
                }
            }
        } else {
            $dir = static::getPhpUnitXmlDir();
        }

        $finder = new Finder();
        $finder->name('CliKernel.php')->depth(0)->in($dir);
        $results = iterator_to_array($finder);
        if (!count($results)) {
            throw new \RuntimeException('Either set KERNEL_DIR in your phpunit.xml according to http://symfony.com/doc/current/book/testing.html#your-first-functional-test or override the WebTestCase::createKernel() method.');
        }

        $file = current($results);
        $class = $file->getBasename('.php');

        require_once $file;

        return $class;
    }
}
