<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\PageObjectExtension\PageObject\Factory;

use Behat\Mink\Mink;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory\ClassNameResolver;
use SensioLabs\Behat\PageObjectExtension\PageObject\InlineElement;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use SensioLabs\Behat\PageObjectExtension\PageObject\PageObject;

final class DefaultFactory implements Factory
{
    /**
     * @var Mink
     */
    private $mink;

    /**
     * @var ClassNameResolver
     */
    private $classNameResolver;

    /**
     * @var array
     */
    private $pageParameters = [];

    /**
     * @param Mink $mink
     * @param ClassNameResolver $classNameResolver
     * @param array $pageParameters
     */
    public function __construct(Mink $mink, ClassNameResolver $classNameResolver, array $pageParameters)
    {
        $this->mink = $mink;
        $this->pageParameters = $pageParameters;
        $this->classNameResolver = $classNameResolver;
    }

    /**
     * @param string $name
     *
     * @return Page
     */
    public function createPage($name)
    {
        $pageClass = $this->classNameResolver->resolvePage($name);

        return $this->instantiatePage($pageClass);
    }

    /**
     * @param string $name
     *
     * @return Element
     */
    public function createElement($name)
    {
        $elementClass = $this->classNameResolver->resolveElement($name);

        return $this->instantiateElement($elementClass);
    }

    /**
     * @param array|string $selector
     *
     * @return InlineElement
     */
    public function createInlineElement($selector)
    {
        return new InlineElement($selector, $this->mink->getSession(), $this);
    }

    /**
     * @param string $pageObjectClass
     *
     * @return PageObject
     */
    public function create($pageObjectClass)
    {
        if (is_subclass_of($pageObjectClass, Page::class)) {
            return $this->instantiatePage($pageObjectClass);
        } elseif (is_subclass_of($pageObjectClass, Element::class)) {
            return $this->instantiateElement($pageObjectClass);
        }

        throw new \InvalidArgumentException(sprintf('Not a page object class: %s', $pageObjectClass));
    }

    /**
     * @param string $pageClass
     *
     * @return Page
     */
    private function instantiatePage($pageClass)
    {
        return new $pageClass($this->mink->getSession(), $this, $this->pageParameters);
    }

    /**
     * @param string $elementClass
     *
     * @return Element
     */
    private function instantiateElement($elementClass)
    {
        return new $elementClass($this->mink->getSession(), $this);
    }
}
