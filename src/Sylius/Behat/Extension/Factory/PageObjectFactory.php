<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Extension\Factory;

use Behat\Mink\Mink;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory\ClassNameResolver;
use SensioLabs\Behat\PageObjectExtension\PageObject\InlineElement;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use SensioLabs\Behat\PageObjectExtension\PageObject\PageObject;
use Symfony\Cmf\Component\Routing\ChainRouterInterface;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class PageObjectFactory implements Factory
{
    /**
     * @var ClassNameResolver
     */
    private $classNameResolver;

    /**
     * @var Factory
     */
    private $defaultFactory;

    /**
     * @var Mink
     */
    private $mink = null;

    /**
     * @var ChainRouterInterface
     */
    private $router;

    /**
     * @var array
     */
    private $pageParameters = array();

    /**
     * @param ClassNameResolver $classNameResolver
     * @param Factory $defaultFactory
     * @param Mink $mink
     * @param ChainRouterInterface $router
     * @param array $pageParameters
     */
    public function __construct(
        ClassNameResolver $classNameResolver,
        Factory $defaultFactory,
        Mink $mink,
        ChainRouterInterface $router,
        array $pageParameters
    ) {
        $this->classNameResolver = $classNameResolver;
        $this->defaultFactory = $defaultFactory;
        $this->mink = $mink;
        $this->router = $router;
        $this->pageParameters = $pageParameters;
    }

    /**
     * @param string $class
     *
     * @return PageObject
     */
    public function instantiate($class)
    {
        return $this->defaultFactory->instantiate($class);
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
        return $this->defaultFactory->createElement($name);
    }

    /**
     * @param string|array $selector
     *
     * @return InlineElement
     */
    public function createInlineElement($selector)
    {
        return $this->defaultFactory->createInlineElement($selector);
    }

    /**
     * @param string $pageObjectClass
     *
     * @return PageObject
     */
    public function create($pageObjectClass)
    {
        return $this->defaultFactory->create($pageObjectClass);
    }

    /**
     * @param string $pageClass
     *
     * @return Page
     */
    private function instantiatePage($pageClass)
    {
        if (!is_subclass_of($pageClass, 'Sylius\Behat\Page\SymfonyPage')) {
            throw new \InvalidArgumentException(sprintf('Invalid page object class: %s, to use this factory you need to extend SymfonyPage', $pageClass));
        }

        return new $pageClass($this->mink->getSession(), $this, $this->pageParameters, $this->router);
    }
}
