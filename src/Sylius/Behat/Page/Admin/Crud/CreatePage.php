<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\Crud;

use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Symfony\Component\Routing\RouterInterface;

class CreatePage extends SymfonyPage implements CreatePageInterface
{
    /** @var string */
    private $routeName;

    public function __construct(Session $session, $minkParameters, RouterInterface $router, string $routeName)
    {
        parent::__construct($session, $minkParameters, $router);

        $this->routeName = $routeName;
    }

    public function create(): void
    {
        $this->getDocument()->pressButton('Create');
    }

    public function getValidationMessage(string $element): string
    {
        $foundElement = $this->getFieldElement($element);
        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Field element');
        }

        $validationMessage = $foundElement->find('css', '.sylius-validation-error');
        if (null === $validationMessage) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.sylius-validation-error');
        }

        return $validationMessage->getText();
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    /**
     * @throws ElementNotFoundException
     */
    private function getFieldElement(string $element): ?\Behat\Mink\Element\NodeElement
    {
        $element = $this->getElement($element);
        while (null !== $element && !$element->hasClass('field')) {
            $element = $element->getParent();
        }

        return $element;
    }
}
