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

use Sylius\Bundle\AdminBundle\Generator\TaxonSlugGeneratorInterface;
use Sylius\Component\Core\Model\Taxon;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent]
class FormComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;
    use LiveCollectionTrait;

    #[LiveProp(fieldName: 'resource')]
    public ?Taxon $resource = null;

    /**
     * @param class-string $formClass
     * @param TaxonRepositoryInterface<TaxonInterface> $taxonRepository
     */
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly string $formClass,
        private readonly TaxonRepositoryInterface $taxonRepository,
        private readonly TaxonSlugGeneratorInterface $slugGenerator,
    ) {
    }

    #[LiveAction]
    public function generateTaxonSlug(#[LiveArg] string $localeCode): void
    {
        $name = $this->formValues['translations'][$localeCode]['name'];
        $parent = $this->taxonRepository->findOneBy(['code' => $this->formValues['parent']]);

        $this->formValues['translations'][$localeCode]['slug'] = $this->slugGenerator->generate($name, $localeCode, $parent);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->create($this->formClass, $this->resource);
    }
}
