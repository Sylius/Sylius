<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Doctrine;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Resource\Event\ResourceEvent;
use Sylius\Component\Resource\Manager\DomainManagerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Domain manager.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DomainManager implements DomainManagerInterface
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var string
     */
    protected $resourceName;

    /**
     * @var string
     */
    protected $bundlePrefix;

    /**
     * @var string
     */
    protected $className;

    public function __construct(ObjectManager $manager, EventDispatcherInterface $eventDispatcher, $bundlePrefix, $resourceName, ClassMetadata $class)
    {
        $this->manager = $manager;
        $this->eventDispatcher = $eventDispatcher;
        $this->bundlePrefix = $bundlePrefix;
        $this->resourceName = $resourceName;
        $this->className = $class->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return new $this->className();
    }

    /**
     * {@inheritdoc}
     */
    public function create($resource = null, $eventName = 'create', $flush = true, $transactional = true)
    {
        if (null === $resource) {
            $resource = $this->createNew();
        }

        return $this->process($resource, 'persist', $eventName, $flush, $transactional);
    }

    /**
     * {@inheritdoc}
     */
    public function update($resource, $eventName = 'update', $flush = true, $transactional = true)
    {
        return $this->process($resource, 'persist', $eventName, $flush, $transactional);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($resource, $eventName = 'delete', $flush = true, $transactional = true)
    {
        return $this->process($resource, 'remove', $eventName, $flush, $transactional);
    }

    /**
     * @param string $name
     * @param Event  $event
     *
     * @return ResourceEvent
     */
    protected function dispatchEvent($name, Event $event)
    {
        return $this->eventDispatcher->dispatch($this->getEventName($name), $event);
    }

    /**
     * @param object $resource
     * @param string $action
     * @param string $eventName
     * @param bool   $flush
     * @param bool   $transactional
     *
     * @return null|object
     *
     * @throws \Exception
     */
    protected function process($resource, $action, $eventName, $flush = true, $transactional = true)
    {
        if (!in_array($action, array('persist', 'remove'))) {
            throw new \InvalidArgumentException(sprintf('Unknown object manager action called "%s".', $action));
        }

        $event = $this->dispatchEvent('pre_'.$eventName, new ResourceEvent($resource));

        if ($event->isStopped()) {
            return null;
        }

        $this->processManageAction($action, $resource, $flush, $transactional);

        $this->dispatchEvent('post_'.$eventName, new ResourceEvent($resource));

        return $resource;
    }

    /**
     * @param string $action
     * @param object $resource
     * @param bool   $flush
     * @param bool   $transactional
     *
     * @throws \Exception
     */
    private function processManageAction($action, $resource, $flush, $transactional)
    {
        if ($this->manager instanceof EntityManagerInterface) {
            if ($transactional) {
                $this->manager->beginTransaction();
            }
            try {
                $this->manager->{$action}($resource);
                if ($flush) {
                    $this->manager->flush();
                }
                if ($transactional) {
                    $this->manager->commit();
                }
            } catch (\Exception $e) {
                if ($transactional) {
                    $this->manager->rollback();
                }

                throw $e;
            }
        } else {
            $this->manager->{$action}($resource);
            if ($flush) {
                $this->manager->flush();
            }
        }
    }

    /**
     * @param string $eventName
     *
     * @return string
     */
    private function getEventName($eventName)
    {
        return sprintf('%s.%s.%s', $this->bundlePrefix, $this->resourceName, $eventName);
    }
}
