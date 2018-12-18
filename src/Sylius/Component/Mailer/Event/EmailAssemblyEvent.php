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

namespace Sylius\Component\Mailer\Event;

use Sylius\Component\Mailer\Model\EmailInterface;
use Symfony\Component\EventDispatcher\Event;

class EmailAssemblyEvent extends Event
{
    /** @var EmailInterface */
    protected $email;

    /** @var mixed[] */
    protected $data;

    public function __construct(EmailInterface $email, array $data = [])
    {
        $this->email = $email;
        $this->data = $data;
    }

    public function getEmail(): EmailInterface
    {
        return $this->email;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function addData(array $data): self
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }
}
