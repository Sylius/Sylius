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

namespace Sylius\Behat\Context\Api\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\RequestFactoryInterface;
use Sylius\Behat\Context\Api\Resources;

final class ContactContext implements Context
{
    private array $content = [];

    public function __construct(
        private RequestFactoryInterface $requestFactory,
        private ApiClientInterface $apiClient,
    ) {
    }

    /**
     * @When I want to request contact
     */
    public function iWantToRequestContact(): void
    {
        //intentionally left empty
    }

    /**
     * @When I specify the message as :message
     */
    public function iSpecifyTheMessage($message): void
    {
        $this->content['message'] = $message;
    }

    /**
     * @When I specify the email as :email
     */
    public function iSpecifyTheEmail($email): void
    {
        $this->content['email'] = $email;
    }

    /**
     * @When I send it
     */
    public function iSendIt(): void
    {
        $request = $this->requestFactory->create(
            'shop',
            Resources::CONTACT_REQUEST,
            'Bearer'
        );

        $request->setContent($this->content);

        $this->apiClient->request($request);
    }
}
