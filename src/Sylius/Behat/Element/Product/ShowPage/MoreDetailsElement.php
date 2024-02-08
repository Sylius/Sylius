<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Element\Product\ShowPage;

use FriendsOfBehat\PageObjectExtension\Element\Element;

final class MoreDetailsElement extends Element implements MoreDetailsElementInterface
{
    public function getDescription(): string
    {
        return $this->getElement('description')->getText();
    }

    public function getProductMetaKeywords(): string
    {
        return $this->getElement('meta_keywords')->getText();
    }

    public function getShortDescription(): string
    {
        return $this->getElement('short_description')->getText();
    }

    public function getMetaDescription(): string
    {
        return $this->getElement('meta_description')->getText();
    }

    public function getSlug(): string
    {
        return $this->getElement('slug')->getText();
    }

    public function getName(): string
    {
        return $this->getElement('name')->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'description' => '#more-details tr:contains("Description") td:nth-child(2)',
            'meta_description' => '#more-details tr:contains("Meta description") td:nth-child(2)',
            'meta_keywords' => '#more-details tr:contains("Meta keywords") td:nth-child(2)',
            'name' => '#more-details tr:contains("Name") td:nth-child(2)',
            'short_description' => '#more-details tr:contains("Short description") td:nth-child(2)',
            'slug' => '#more-details tr:contains("Slug") td:nth-child(2)',
        ]);
    }
}
