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

final class TranslationsElement extends Element implements TranslationsElementInterface
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
            'description' => '#product-translations [data-test-description]',
            'meta_description' => '#product-translations [data-test-meta-description]',
            'meta_keywords' => '#product-translations [data-test-meta-keywords]',
            'name' => '#product-translations [data-test-product-name]',
            'short_description' => '#product-translations [data-test-short-description]',
            'slug' => '#product-translations [data-test-slug]',
        ]);
    }
}
