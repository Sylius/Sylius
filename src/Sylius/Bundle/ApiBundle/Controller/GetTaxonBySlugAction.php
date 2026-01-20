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

use ApiPlatform\Metadata\IriConverterInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class GetTaxonBySlugAction
{
    public function __construct(
        private LocaleContextInterface $localeContext,
        private TaxonRepositoryInterface $taxonRepository,
        private IriConverterInterface $iriConverter,
        private RequestStack $requestStack,
    ) {
    }

    public function __invoke(string $slug): RedirectResponse
    {
        $locale = $this->localeContext->getLocaleCode();

        $taxon = $this->taxonRepository->findOneBySlug($slug, $locale);

        if (null === $taxon) {
            throw new NotFoundHttpException('Not Found');
        }

        $iri = $this->iriConverter->getIriFromResource($taxon);

        $request = $this->requestStack->getCurrentRequest();

        $requestQuery = $request->getQueryString();
        if (null !== $requestQuery) {
            $iri .= sprintf('?%s', $requestQuery);
        }

        return new RedirectResponse($iri, Response::HTTP_MOVED_PERMANENTLY);
    }
}
