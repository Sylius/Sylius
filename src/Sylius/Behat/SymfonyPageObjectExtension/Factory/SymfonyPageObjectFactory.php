<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\SymfonyPageObjectExtension\Factory;

use Behat\Mink\Mink;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory\ClassNameResolver;
use Sylius\Behat\SymfonyPageObjectExtension\PageObject\SymfonyPage;
use Symfony\Cmf\Component\Routing\ChainRouterInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class SymfonyPageObjectFactory implements Factory
{
    /**
     * @var Factory
     */
    private $decoratedFactory;

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
     * @var ChainRouterInterface
     */
    private $router;

    /**
     * @param Factory $decoratedFactory
     * @param Mink $mink
     * @param ClassNameResolver $classNameResolver
     * @param array $pageParameters
     * @param ChainRouterInterface $router
     */
    public function __construct(
        Factory $decoratedFactory,
        Mink $mink,
        ClassNameResolver $classNameResolver,
        array $pageParameters,
        ChainRouterInterface $router
    ) {
        $this->decoratedFactory = $decoratedFactory;
        $this->mink = $mink;
        $this->classNameResolver = $classNameResolver;
        $this->pageParameters = $pageParameters;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function createPage($name)
    {
        $pageClass = $this->classNameResolver->resolvePage($name);

        return $this->create($pageClass);
    }

    /**
     * {@inheritdoc}
     */
    public function createElement($name)
    {
        return $this->decoratedFactory->createElement($name);
    }

    /**
     * {@inheritdoc}
     */
    public function createInlineElement($selector)
    {
        return $this->decoratedFactory->createInlineElement($selector);
    }

    /**
     * {@inheritdoc}
     */
    public function create($pageObjectClass)
    {
        try {
            return $this->instantiatePage($pageObjectClass);
        } catch (\InvalidArgumentException $exception) {
            return $this->decoratedFactory->create($pageObjectClass);
        }
    }

    /**
     * @param string $pageClass
     *
     * @return SymfonyPage
     */
    private function instantiatePage($pageClass)
    {
        if (!is_subclass_of($pageClass, SymfonyPage::class)) {
            throw new \InvalidArgumentException(sprintf('Invalid page object class: %s, to use this factory you need to extend SymfonyPage', $pageClass));
        }

        return new $pageClass($this->mink->getSession(), $this, $this->pageParameters, $this->router);
    }
}
