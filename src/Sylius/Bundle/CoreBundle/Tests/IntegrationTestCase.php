<?php

namespace Sylius\Bundle\CoreBundle\Tests;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Prophecy\Prophet;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class IntegrationTestCase extends WebTestCase
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var ContainerInterface */
    protected $container;

    /** @var Client */
    protected $client;

    /** @var  Prophet */
    protected $prophet;

    /** @var bool set it to true if you would like to clear databases before tests */
    protected $useDatabase = false;

    public function setUp()
    {
        parent::setUp();

        $this->prophet = new Prophet();
        $this->client = $this->createClient();
        $this->container = static::$kernel->getContainer();
        $this->entityManager = $this->container->get('doctrine.orm.entity_manager');
        $this->eventDispatcher = $this->container->get('event_dispatcher');

        if ($this->useDatabase) {
            $ormPurger = new ORMPurger($this->entityManager);
            $ormPurger->purge();
        }

        $this->entityManager->beginTransaction();
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->prophet->checkPredictions();
        $this->entityManager->rollback();
    }
}