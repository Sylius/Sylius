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

use Sylius\Behat\Page\SymfonyPage;

class ErrorPage extends SymfonyPage implements ErrorPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return '_twig_error_test';
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
            'title' => 'h2',
        ]);
    }
}
