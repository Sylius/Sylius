<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class ResourceBuilder implements ResourceBuilderInterface
{
    /**
     * Currently built resource.
     *
     * @var object
     */
    protected $resource;

    /**
     * Object manager.
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Object repository.
     *
     * @var RepositoryInterface
     */
    protected $objectRepository;

    public function __construct(ObjectManager $objectManager, RepositoryInterface $objectRepository)
    {
        $this->objectManager = $objectManager;
        $this->objectRepository = $objectRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data = array())
    {
        $this->resource = $this->objectRepository->createNew();

        // TODO : automatically set $data

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        return $this->resource;
    }

    /**
     * {@inheritdoc}
     */
    public function save($flush = true)
    {
        $this->objectManager->persist($this->resource);

        if ($flush) {
            $this->objectManager->flush();
        }

        return $this->resource;
    }

    /**
     * {@inheritdoc}
     */
    public function __call($method, $arguments)
    {
        if (!method_exists($this->resource, $method)) {
            throw new \BadMethodCallException(sprintf('Resource has no "%s()" method.', $method));
        }

        call_user_func_array(array($this->resource, $method), $arguments);

        return $this;
    }
} 