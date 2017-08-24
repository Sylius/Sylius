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

namespace Sylius\Bundle\TaxonomyBundle\Controller;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Generator\TaxonSlugGeneratorInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class TaxonSlugController
{
    /** @var TaxonSlugGeneratorInterface */
    private $taxonSlugGenerator;

    /** @var RepositoryInterface */
    private $taxonRepository;

    /** @var FactoryInterface */
    private $taxonFactory;

    public function __construct(
        TaxonSlugGeneratorInterface $taxonSlugGenerator,
        RepositoryInterface $taxonRepository,
        FactoryInterface $taxonFactory
    ) {
        $this->taxonSlugGenerator = $taxonSlugGenerator;
        $this->taxonRepository = $taxonRepository;
        $this->taxonFactory = $taxonFactory;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function generateAction(Request $request): Response
    {
        $name = $request->query->get('name');
        $locale = $request->query->get('locale');
        $parentId = $request->query->get('parentId');

        /** @var TaxonInterface $taxon */
        $taxon = $this->taxonFactory->createNew();
        $taxon->setCurrentLocale($locale);
        $taxon->setFallbackLocale($locale);
        $taxon->setName($name);

        if (null !== $parentId) {
            $taxon->setParent($this->taxonRepository->find($parentId));
        }

        return new JsonResponse([
            'slug' => $this->taxonSlugGenerator->generate($taxon, $locale),
        ]);
    }
}
