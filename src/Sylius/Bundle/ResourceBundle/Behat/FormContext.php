<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Behat;

use Behat\Mink\Element\Element;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Selector\Xpath\Escaper;
use Behat\MinkExtension\Context\RawMinkContext;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
abstract class FormContext extends RawMinkContext
{
    /**
     * @return string
     */
    protected function getDefaultCollectionSelector()
    {
        return 'div[@data-form-type="collection"]';
    }

    /**
     * @param string $position
     *
     * @return string
     */
    protected function getDefaultCollectionItemSelector($position)
    {
        return sprintf(
            '*[contains(@data-form-collection, "item") and position()=%d]',
            $position
        );
    }

    /**
     * Add an item of the collection type
     *
     * @param string $collectionSelector
     * @param null   $buttonName
     * @param string $buttonType
     */
    protected function addCollectionItem($collectionSelector = null, $buttonName = null, $buttonType = 'click')
    {
        $collectionSelector = null !== $collectionSelector ? $collectionSelector : $this->getDefaultCollectionSelector();
        $buttonSelector = null === $buttonName ? '' : sprintf('and text()[contains(., "%s")]', $buttonName);

        $button = $this->findElement(
            sprintf(
                '//%s//*[contains(@data-form-collection, "add") %s]',
                $collectionSelector,
                $buttonSelector
            )
        );

        $button->$buttonType();
    }

    /**
     * Delete an item of the collection type
     *
     * @param string  $collectionSelector
     * @param integer $position
     * @param null    $buttonName
     * @param string  $buttonType
     */
    protected function deleteCollectionItem(
        $position,
        $buttonName = null,
        $buttonType = 'click',
        $collectionSelector = null
    ) {
        $collectionSelector = null !== $collectionSelector ? $collectionSelector : $this->getDefaultCollectionSelector();
        $buttonSelector = null === $buttonName ? '' : sprintf('and text()[contains(., "%s")', $buttonName);

        $button = $this->findElement(
            sprintf(
                '//%s//%s//*[contains(@data-form-collection, "delete") %s]',
                $collectionSelector,
                $this->getDefaultCollectionItemSelector($position),
                $buttonSelector
            )
        );

        $button->$buttonType();
    }

    /**
     * Check if the value of the field is valid
     *
     * @param  string           $position
     * @param  string           $label
     * @param  string           $collectionSelector
     * @return NodeElement|null
     */
    protected function isInvalidCollectionField($position, $label, $collectionSelector = null)
    {
        $collectionSelector = null !== $collectionSelector ? $collectionSelector : $this->getDefaultCollectionSelector();

        try {
            return $this->findElement(
                sprintf(
                    '//%s//%s//%s',
                    $collectionSelector,
                    $this->getDefaultCollectionItemSelector($position),
                    sprintf('*[contains(@class, "has-error")]//*[contains(@name, "%s")]', strtolower($label))
                )
            );
        } catch (ElementNotFoundException $e) {
            return $this->findElement(
                sprintf(
                    '//%s//%s//%s',
                    $collectionSelector,
                    $this->getDefaultCollectionItemSelector($position),
                    sprintf('*[contains(@class, "has-error")]//label[text()[contains(., "%s")]]', $label)
                )
            );
        }
    }

    /**
     * Fill a collection form field
     *
     * @param integer $position
     * @param string  $field
     * @param mixed   $value
     * @param string  $collectionSelector
     */
    protected function fillCollectionField($position, $field, $value, $collectionSelector = null)
    {
        $collectionSelector = null !== $collectionSelector ? $collectionSelector : $this->getDefaultCollectionSelector();

        $collectionElement = $this->findElement(
            sprintf(
                '//%s//%s',
                $collectionSelector,
                $this->getDefaultCollectionItemSelector($position)
            )
        );

        $this->fillInField($field, $value, $collectionElement);
    }

    /**
     * @param string  $locator
     * @param string  $value
     * @param Element $container
     */
    protected function fillInField($locator, $value, Element $container = null)
    {
        try {
            $field = $this->findField($locator, $container);
        } catch (ElementNotFoundException $e) {
            $field = $this->findElement(
                sprintf('//*[contains(@name, "%s")]', strtolower($locator)),
                'xpath',
                $container
            );
        }

        if ($field->getTagName() === 'select') {
            $field->selectOption($value);
        } else {
            $field->setValue($value);
        }
    }

    /**
     * @param string  $locator
     * @param Element $container
     *
     * @return NodeElement
     *
     * @throws ElementNotFoundException
     */
    protected function findField($locator, Element $container = null)
    {
        $escaper = new Escaper();

        if (null !== $container) {
            $field = $container->find('named', array(
                'field', $escaper->escapeLiteral($locator),
            ));
        } else {
            $field = $this->getSession()->getPage()->findField($locator);
        }

        if (null === $field) {
            throw new ElementNotFoundException(
                $this->getSession(),
                'element',
                'xpath',
                $escaper->escapeLiteral($locator)
            );
        }

        return $field;
    }

    /**
     * @param string  $locator
     * @param string  $selector
     * @param Element $container
     *
     * @return NodeElement
     *
     * @throws ElementNotFoundException
     */
    protected function findElement($locator, $selector = 'xpath', Element $container = null)
    {
        $escaper = new Escaper();
        if (null !== $container) {
            $field = $container->find($selector, $locator);
        } else {
            $field = $this->getSession()->getPage()->find($selector, $locator);
        }

        if (null === $field) {
            throw new ElementNotFoundException(
                $this->getSession(),
                'element',
                'xpath',
                $escaper->escapeLiteral($locator)
            );
        }

        return $field;
    }
}
