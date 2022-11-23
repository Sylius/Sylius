<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Converter;

use Payum\Core\Reply\ReplyInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

interface PayumReplyConverterInterface
{
    public function convert(ReplyInterface $reply): JsonResponse;
}
