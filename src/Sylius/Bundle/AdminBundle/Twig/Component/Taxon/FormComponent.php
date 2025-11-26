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
use Sylius\Bundle\UiBundle\Twig\Component\LiveCollectionTrait;
use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponentTrait;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;

#[AsLiveComponent]
class FormComponent
{
    use LiveCollectionTrait;

    /** @use ResourceFormComponentTrait<TaxonInterface> */
    use ResourceFormComponentTrait;

    use TemplatePropTrait;

    /**
     * @param RepositoryInterface<TaxonInterface> $repository
     * @param class-string $resourceClass
     * @param class-string $formClass
     */
    public function __construct(
        RepositoryInterface $repository,
        FormFactoryInterface $formFactory,
        string $resourceClass,
        string $formClass,
        protected readonly TaxonSlugGeneratorInterface $slugGenerator,
    ) {
        $this->initialize($repository, $formFactory, $resourceClass, $formClass);
    }

    #[LiveAction]
    public function generateTaxonSlug(#[LiveArg] string $localeCode): void
    {
        $name = $this->formValues['translations'][$localeCode]['name'];
        $parent = $this->repository->findOneBy(['code' => $this->formValues['parent']]);

        $this->formValues['translations'][$localeCode]['slug'] = $this->slugGenerator->generate($name, $localeCode, $parent);
    }
}
