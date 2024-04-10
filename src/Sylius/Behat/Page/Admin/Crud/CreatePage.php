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

namespace Sylius\Behat\Page\Admin\Crud;

use Behat\Mink\Exception\DriverException;
use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;
use Symfony\Component\Routing\RouterInterface;

class CreatePage extends SymfonyPage implements CreatePageInterface
{
    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        private string $routeName,
    ) {
        parent::__construct($session, $minkParameters, $router);
    }

    public function create(): void
    {
        $this->getDocument()->pressButton('Create');
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    public function getMessageInvalidForm(): string
    {
        return $this->getDocument()->find('css', '.ui.icon.negative.message')->getText();
    }

    protected function verifyStatusCode(): void
    {
        try {
            $statusCode = $this->getSession()->getStatusCode();
        } catch (DriverException) {
            return; // Ignore drivers which cannot check the response status code
        }

        if (($statusCode >= 200 && $statusCode <= 299) || $statusCode === 422) {
            return;
        }

        $currentUrl = $this->getSession()->getCurrentUrl();
        $message = sprintf('Could not open the page: "%s". Received an error status code: %s', $currentUrl, $statusCode);

        throw new UnexpectedPageException($message);
    }
}
