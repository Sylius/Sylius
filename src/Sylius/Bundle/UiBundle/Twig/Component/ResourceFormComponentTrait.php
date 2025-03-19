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

namespace Sylius\Bundle\UiBundle\Twig\Component;

use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Model\ResourceInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

/** @template T of ResourceInterface */
trait ResourceFormComponentTrait
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;
    use HookableLiveComponentTrait;

    /** @var T|null */
    #[LiveProp(hydrateWith: 'hydrateResource', dehydrateWith: 'dehydrateResource')]
    public ?ResourceInterface $resource = null;

    /** @var RepositoryInterface<T> */
    protected RepositoryInterface $repository;

    protected FormFactoryInterface $formFactory;

    /** @var class-string */
    protected string $resourceClass;

    /** @var class-string */
    protected string $formClass;

    /** @return T|null */
    public function hydrateResource(mixed $value): ?ResourceInterface
    {
        if (empty($value)) {
            return $this->createResource();
        }

        return $this->repository->find($value);
    }

    /** @param T|null $resource */
    public function dehydrateResource(ResourceInterface|null $resource): mixed
    {
        return $resource?->getId();
    }

    /**
     * @param RepositoryInterface<T> $repository
     *
     * @phpstan-return void
     */
    protected function initialize(
        RepositoryInterface $repository,
        FormFactoryInterface $formFactory,
        string $resourceClass,
        string $formClass,
    ) {
        $this->repository = $repository;
        $this->formFactory = $formFactory;
        $this->resourceClass = $resourceClass;
        $this->formClass = $formClass;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->create($this->formClass, $this->resource);
    }

    /** @return T */
    protected function createResource(): ResourceInterface
    {
        return new $this->resourceClass();
    }
}
