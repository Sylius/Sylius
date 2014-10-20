<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\Builder;

use Faker\Factory as FakerFactory;
use Faker\Generator;

/**
 * Abstract data set builder.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
abstract class AbstractBuilder implements BuilderInterface
{
    /**
     * @var string
     */
    protected $model;

    /**
     * @var Generator
     */
    private $faker;

    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataSet($name = 'default')
    {
        $getter = 'getDataSet'.ucfirst($name);
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        throw new \Exception(sprintf('The data set %s has not been created.', $name));
    }

    /**
     * {@inheritdoc}
     */
    public function getRandomDataSet()
    {
        //TODO
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function getResource($name)
    {
        $getter = 'getResource'.ucfirst($name);
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        throw new \Exception(sprintf('The element %s has not been created.', $name));
    }

    /**
     * Build a resource with the provided data.
     *
     * @param array $data
     *
     * @return mixed
     */
    protected function buildWithData(array $data)
    {
        $class = $this->model;
        $resource = new $class;

        foreach ($data as $key => $value)
        {
            $setter = 'set'.ucfirst($key);
            $resource->$setter($value);
        }

        return $resource;
    }

    /**
     * Build a resource with Faker.
     *
     * @throws \Exception
     */
    protected function buildWithFaker()
    {
        throw new \Exception('The method %s has to be implemented before being called.', __METHOD__);
    }

    /**
     * Get an instance of Faker generator.
     *
     * @return Generator
     */
    protected function getFaker()
    {
        if (null === $this->faker) {
            $this->faker = FakerFactory::create();
        }

        return $this->faker;
    }
} 