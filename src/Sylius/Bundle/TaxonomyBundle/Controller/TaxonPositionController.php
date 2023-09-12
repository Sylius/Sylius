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

use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class TaxonPositionController
{
    public function __construct(
        private TaxonRepositoryInterface $taxonRepository,
        private ObjectManager $taxonManager,
    ) {
    }

    public function moveUpAction(int $id): Response
    {
        /** @var TaxonInterface|null $taxonToBeMoved */
        $taxonToBeMoved = $this->taxonRepository->find($id);
        Assert::notNull($taxonToBeMoved);

        if ($taxonToBeMoved->getPosition() > 0) {
            $taxonToBeMoved->setPosition($taxonToBeMoved->getPosition() - 1);
        }
        $this->taxonManager->flush();

        return new JsonResponse();
    }

    public function moveDownAction(int $id): Response
    {
        /** @var TaxonInterface|null $taxonToBeMoved */
        $taxonToBeMoved = $this->taxonRepository->find($id);
        Assert::notNull($taxonToBeMoved);

        $taxonToBeMoved->setPosition($taxonToBeMoved->getPosition() + 1);
        $this->taxonManager->flush();

        return new JsonResponse();
    }
}
