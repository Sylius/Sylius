<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\PageObjectExtension\Context\Argument;

use Behat\Behat\Context\Argument\ArgumentResolver;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory;
use Sylius\Behat\PageObjectExtension\PageObject\Page;

class PageObjectArgumentResolver implements ArgumentResolver
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveArguments(\ReflectionClass $classReflection, array $arguments)
    {
        $parameters = $this->getConstructorParameters($classReflection);

        foreach ($parameters as $i => $parameter) {
            $parameterClassName = $this->getClassName($parameter);

            if (null !== $parameterClassName && $this->isPageOrElement($parameterClassName)) {
                $arguments[$i] = $this->factory->create($parameterClassName);
            }
        }

        return $arguments;
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    private function isPageOrElement($className)
    {
        return $this->isPage($className) || $this->isElement($className);
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    private function isPage($className)
    {
        return is_subclass_of($className, Page::class);
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    private function isElement($className)
    {
        return is_subclass_of($className, Element::class);
    }

    /**
     * @param \ReflectionClass $classReflection
     *
     * @return \ReflectionParameter[]
     */
    private function getConstructorParameters(\ReflectionClass $classReflection)
    {
        return $classReflection->getConstructor() ? $classReflection->getConstructor()->getParameters() : [];
    }

    /**
     * @param \ReflectionParameter $parameter
     *
     * @return string|null
     */
    private function getClassName(\ReflectionParameter $parameter)
    {
        return $parameter->getClass() ? $parameter->getClass()->getName() : null;
    }
}
