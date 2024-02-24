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

use ApiPlatform\Api\IriConverterInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webmozart\Assert\Assert;

final class GetProductBySlugAction
{
    public function __construct(
        private ChannelContextInterface $channelContext,
        private LocaleContextInterface $localeContext,
        private ProductRepositoryInterface $productRepository,
        private IriConverterInterface $iriConverter,
        private RequestStack $requestStack,
    ) {
    }

    public function __invoke(string $slug): RedirectResponse
    {
        $channel = $this->channelContext->getChannel();
        Assert::isInstanceOf($channel, ChannelInterface::class);
        $locale = $this->localeContext->getLocaleCode();

        $product = $this->productRepository->findOneByChannelAndSlug($channel, $locale, $slug);

        if (null === $product) {
            throw new NotFoundHttpException('Not Found');
        }

        $iri = $this->iriConverter->getIriFromResource($product);

        $request = $this->requestStack->getCurrentRequest();

        $requestQuery = $request->getQueryString();
        if (null !== $requestQuery) {
            $iri .= sprintf('?%s', $requestQuery);
        }

        return new RedirectResponse($iri, Response::HTTP_MOVED_PERMANENTLY);
    }
}
