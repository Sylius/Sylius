<?php

namespace Sylius\Bundle\FixturesBundle\Loader;

use Faker\Factory as FakerFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Faker\Generator;
use Sylius\Bundle\FixturesBundle\Builder\BuilderInterface;
use Sylius\Bundle\FixturesBundle\Builder\GroupBuilder;

class Loader implements LoaderInterface
{
    /**
     * Faker.
     *
     * @var Generator
     */
    protected $faker;

    public static function loadSet($type, $suite = 'default')
    {
        $builder = self::getBuilder($type);

        if (null === $suite)
        {
            return $builder->getRandomSet();
        }

        if (null !== $builder->getSet($suite)) {
            return $builder->getSet($suite);
        }

        return new ArrayCollection();
    }

    /**
     * @param $type
     * @return BuilderInterface
     * @throws \Exception
     */
    protected function getBuilder($type)
    {
        switch ($type) {
            case 'group':
                return new GroupBuilder();
            default:
                throw new \Exception(sprintf('Data of type %s is not handled yet', $type));
        }
    }

    /**
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