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

namespace Sylius\Bundle\ApiBundle\Controller;

use Sylius\Bundle\ApiBundle\Query\Admin\GetSalesStatistics;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Serializer\SerializerInterface;
use Webmozart\Assert\Assert;

final class GetSalesStatisticsAction
{
    public function __construct(
        private MessageBusInterface $queryBus,
        private ChannelContextInterface $channelContext,
        private SerializerInterface $serializer,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $channelCode = $request->query->get('channelCode') ?? $this->channelContext->getChannel()->getCode();

        try {
            $envelope = $this->queryBus->dispatch(
                new GetSalesStatistics(channelCode: $channelCode),
            );
        } catch (HandlerFailedException $exception) {
            return new JsonResponse(['error' => $exception->getPrevious()->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);
        Assert::notNull($handledStamp);

        return new JsonResponse($this->serializer->serialize($handledStamp->getResult(), 'json'), Response::HTTP_OK, [], true);
    }
}
