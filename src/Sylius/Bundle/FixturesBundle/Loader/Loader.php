<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\Loader;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Bundle\FixturesBundle\Builder\BuilderInterface;

/**
 * Data set loader.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class Loader implements LoaderInterface
{
    /**
     * @var array|BuilderInterface
     */
    protected $builders = array();

    /**
     * {@inheritdoc}
     */
    public function loadSet($type, $name = 'default')
    {
        $builder = $this->getBuilder($type);

        if (null === $name)
        {
            return $builder->getRandomDataSet();
        }

        if (null !== $builder->getDataSet($name)) {
            return $builder->getDataSet($name);
        }

        return new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getBuilder($type)
    {
        if (array_key_exists($type.'builder', $this->builders)) {
            return $this->builders[$type.'builder'];
        }

        throw new \Exception(sprintf('Data of type %s is not handled yet', $type));
    }

    /**
     * {@inheritdoc}
     */
    public function setBuilder(BuilderInterface $builder)
    {
        $className = strtolower(get_class($builder));
        if (false !== $pos = strrpos($className, '\\')) {
            $className = substr($className, $pos + 1);
        }

        $this->builders[$className] = $builder;

        return $this;
    }

} 