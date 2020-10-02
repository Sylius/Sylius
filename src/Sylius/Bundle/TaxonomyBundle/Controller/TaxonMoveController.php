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

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class TaxonMoveController
{
    /** @var TaxonRepositoryInterface */
    private $taxonRepository;

    public function __construct(
        TaxonRepositoryInterface $taxonRepository
    ) {
        $this->taxonRepository = $taxonRepository;
    }

    public function upAction(Request $request): Response
    {
        $id = $request->attributes->get('id');
        $limit = $request->attributes->get('limit', null);

        /** @var TaxonInterface|null $taxon */
        $taxon = $this->taxonRepository->find($id);
        Assert::notNull($taxon);

        do {
            $above = $this->taxonRepository->findOneAbove($taxon);
            if (null === $above) {
                break;
            }

            $position = $taxon->getPosition();
            $taxon->setPosition($above->getPosition());
            $above->setPosition($position);

            $this->taxonRepository->add($taxon);
            $this->taxonRepository->add($above);
        } while (null === $limit || --$limit <= 0);

        return new JsonResponse();
    }

    public function downAction(Request $request): Response
    {
        $id = $request->attributes->get('id');
        $limit = $request->attributes->get('limit', null);

        /** @var TaxonInterface|null $taxon */
        $taxon = $this->taxonRepository->find($id);
        Assert::notNull($taxon);

        do {
            $below = $this->taxonRepository->findOneBelow($taxon);
            if (null === $below) {
                break;
            }

            $position = $taxon->getPosition();
            $taxon->setPosition($below->getPosition());
            $below->setPosition($position);

            $this->taxonRepository->add($taxon);
            $this->taxonRepository->add($below);
        } while (null === $limit || --$limit <= 0);

        return new JsonResponse();
    }
}
