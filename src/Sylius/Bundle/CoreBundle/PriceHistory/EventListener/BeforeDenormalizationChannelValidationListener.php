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

namespace Sylius\Bundle\CoreBundle\PriceHistory\EventListener;

use ApiPlatform\Symfony\Validator\Exception\ValidationException;
use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class BeforeDenormalizationChannelValidationListener
{
    public function __construct(
        private ValidatorInterface $validator,
        private ChannelFactoryInterface $channelFactory,
        private array $validationGroups = [],
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $parameters = $request->attributes->all();

        if (
            !is_a($parameters['_api_resource_class'] ?? null, ChannelInterface::class, true) ||
            $request->isMethodSafe() ||
            $request->isMethod(Request::METHOD_DELETE)
        ) {
            return;
        }

        /** @var string $requestContent */
        $requestContent = $request->getContent(false);
        $content = (array) json_decode($requestContent, true, 512, \JSON_THROW_ON_ERROR);
        $channelModel = $this->channelFactory->createNew();

        $violations = $this->validator->startContext()->getViolations();
        foreach ($content as $key => $value) {
            $propertyViolations = $this->validator->validatePropertyValue(
                $channelModel,
                $key,
                $value,
                $this->validationGroups,
            );

            if ($propertyViolations->count() > 0) {
                $violations->addAll($propertyViolations);
            }
        }

        if ($violations->count() > 0) {
            throw new ValidationException($violations);
        }
    }
}
