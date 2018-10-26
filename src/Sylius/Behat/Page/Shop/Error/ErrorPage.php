<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Error;

use Sylius\Behat\Page\Page;

class ErrorPage extends Page implements ErrorPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUrl(array $urlParameters = [])
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
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'title' => 'h1.exception-message',
        ]);
    }
}
