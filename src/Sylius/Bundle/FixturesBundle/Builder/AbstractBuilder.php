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

    abstract public function getSetDefault();

    public function getSet($name = 'default')
    {
        $getter = 'getSuite'.ucfirst($name);
        if (method_exists($this, $getter)) {
            return $this->$getter;
        }

        throw new \Exception(sprintf('The suite %s has not been created.', $name));
    }

    public function getElement($name)
    {
        $getter = 'getElement'.ucfirst($name);
        if (method_exists($this, $getter)) {
            return $this->$getter;
        }

        throw new \Exception(sprintf('The element %s has not been created.', $name));
    }

    public function getRandomSet()
    {
        //TODO
        return;
    }

    protected function buildWithData(array $data)
    {
        $class = $this->getModelClass();
        $resource = new $class;

        foreach ($data as $key => $value)
        {
            $setter = 'set'.ucfirst($key);
            $resource->$setter($value);
        }

        return $resource;
    }

    protected function buildWithFaker()
    {
        throw new \Exception('The method %s has to be implemented before being called.', __METHOD__);
    }
} 