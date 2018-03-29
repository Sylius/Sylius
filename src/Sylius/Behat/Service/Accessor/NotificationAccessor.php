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

namespace Sylius\Behat\Service\Accessor;

use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;

final class NotificationAccessor implements NotificationAccessorInterface
{
    /**
     * @var Session
     */
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageElements(): array
    {
        $messageElements = $this->session->getPage()->findAll('css', '.sylius-flash-message');

        if (empty($messageElements)) {
            throw new ElementNotFoundException($this->session->getDriver(), 'message element', 'css', '.sylius-flash-message');
        }

        return $messageElements;
    }
}
