<?php

namespace Sylius\Bundle\FixturesBundle\Builder;

use Faker\Generator;

abstract class AbstractBuilder implements BuilderInterface
{
    /**
     * @var Generator
     */
    protected $faker;

    public function __construct(Generator $faker)
    {
        $this->faker = $faker;
    }

    public function getSet($name = 'default')
    {
        $getter = 'getSuite'.ucfirst($name);
        if (method_exists($this, $getter)) {
            return $this->$getter;
        }

        throw new \Exception(sprintf('The suite %s has not been created.', $name));
    }

    public function getRandomSet()
    {
        //TODO
        return;
    }

    public function getResource($name)
    {
        $getter = 'getResource'.ucfirst($name);
        if (method_exists($this, $getter)) {
            return $this->$getter;
        }

        throw new \Exception(sprintf('The element %s has not been created.', $name));
    }

    /**
     * Build a resource with the provided data.
     *
     * @param array $data
     * @return mixed
     */
    protected function buildWithData(array $data)
    {
        $class = $this->getResourceClass();
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
} 