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

namespace Sylius\Behat\Page;

use FriendsOfBehat\PageObjectExtension\Page\Page;

class ErrorPage extends Page implements ErrorPageInterface
{
    /**
     * {@inheritdoc}
     */
    protected function getUrl(array $urlParameters = []): string
    {
        // This page does not have any url
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return $this->getElement('title')->getText();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'title' => 'h1.exception-message',
        ]);
    }
}
