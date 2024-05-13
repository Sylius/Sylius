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

namespace Sylius\Bundle\AdminBundle\Twig\Component\Taxon;

use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
class TreeComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;

    /**
     * @param TaxonRepositoryInterface<TaxonInterface> $taxonRepository
     */
    public function __construct(
        private TaxonRepositoryInterface $taxonRepository,
        private ObjectManager $taxonManager,
    ) {
    }

    /**
     * @return array<TaxonInterface>
     */
    #[ExposeInTemplate]
    public function getRootNodes(): array
    {
        return $this->taxonRepository->findHydratedRootNodes();
    }

    #[LiveAction]
    public function moveUp(#[LiveArg] int $taxonId): void
    {
        $taxonToBeMoved = $this->taxonRepository->find($taxonId);

        if ($taxonToBeMoved->getPosition() > 0) {
            $taxonToBeMoved->setPosition($taxonToBeMoved->getPosition() - 1);
            $this->taxonManager->flush();
        }
    }

    #[LiveAction]
    public function moveDown(#[LiveArg] int $taxonId): void
    {
        $taxonToBeMoved = $this->taxonRepository->find($taxonId);

        $taxonToBeMoved->setPosition($taxonToBeMoved->getPosition() + 1);
        $this->taxonManager->flush();
    }

    #[LiveAction]
    public function delete(#[LiveArg] int $taxonId): void
    {
        $taxon = $this->taxonRepository->find($taxonId);
        $this->taxonRepository->remove($taxon);
    }
}
