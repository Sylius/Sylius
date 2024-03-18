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

namespace Sylius\Bundle\TaxonomyBundle\Controller;

use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webmozart\Assert\Assert;

final class TaxonPositionController
{
    /**
     * @param TaxonRepositoryInterface<TaxonInterface> $taxonRepository
     */
    public function __construct(
        private TaxonRepositoryInterface $taxonRepository,
        private ObjectManager $taxonManager,
    ) {
    }

    public function moveUpAction(int $id): Response
    {
        $taxonToBeMoved = $this->findTaxonOr404($id);

        if ($taxonToBeMoved->getPosition() > 0) {
            $taxonToBeMoved->setPosition($taxonToBeMoved->getPosition() - 1);
            $this->taxonManager->flush();
        }

        return new JsonResponse('', Response::HTTP_NO_CONTENT);
    }

    public function moveDownAction(int $id): Response
    {
        $taxonToBeMoved = $this->findTaxonOr404($id);

        $taxonToBeMoved->setPosition($taxonToBeMoved->getPosition() + 1);
        $this->taxonManager->flush();

        return new JsonResponse('', Response::HTTP_NO_CONTENT);
    }

    private function findTaxonOr404(int $id): TaxonInterface
    {
        /** @var TaxonInterface|null $taxon */
        $taxon = $this->taxonRepository->find($id);

        if (null === $taxon) {
            throw new NotFoundHttpException(sprintf('Taxon with id %d does not exist.', $id));
        }

        Assert::isInstanceOf($taxon, TaxonInterface::class);

        return $taxon;
    }
}
