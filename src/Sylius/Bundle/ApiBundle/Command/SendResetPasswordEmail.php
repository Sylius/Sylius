<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Command;

class SendResetPasswordEmail
{
    /** @var string */
    public $email;

    /** @var string */
    private $channelCode;

    /** @var string */
    private $localeCode;

    public function __construct(
        string $email,
        string $channelCode,
        string $localeCode
    ) {
        $this->email = $email;
        $this->channelCode = $channelCode;
        $this->localeCode = $localeCode;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function channelCode(): string
    {
        return $this->channelCode;
    }

    public function localeCode(): string
    {
        return $this->localeCode;
    }
}
