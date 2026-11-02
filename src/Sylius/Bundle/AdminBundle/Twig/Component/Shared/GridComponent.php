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

namespace Sylius\Bundle\AdminBundle\Twig\Component\Shared;

use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Twig\Component\ComponentWithGridFiltersFormTrait;
use Sylius\Resource\Grid\View\ContextualGridView;
use Sylius\Resource\Grid\View\Factory\ContextualGridViewFactoryInterface;
use Sylius\Resource\Grid\View\GridContext;
use Sylius\Resource\Metadata\MetadataInterface;
use Sylius\Resource\Metadata\RegistryInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

/**
 * Generic live grid of the admin index pages.
 *
 * The resource controller builds the grid from the current request, which is not available anymore during
 * live re-renders. The grid is therefore built from a grid context: the grid traits keep the route parameters
 * of the index page as the grid variables, so that the grid configuration keeps working ("$productId",
 * "@=variables['productId']", "resource.id"...), as on the regular index page, and the action templates guess
 * their routes with "grid.gridContext.routeName(...)". The filters are rendered in a single form bound to the criteria.
 *
 * @experimental
 */
class GridComponent
{
    use ComponentWithGridFiltersFormTrait;
    use DefaultActionTrait;
    use HookableLiveComponentTrait;
    use TemplatePropTrait;

    #[LiveProp]
    public string $resourceAlias;

    #[LiveProp]
    public string $gridName;

    #[LiveProp]
    public string $section = 'admin';

    public function __construct(
        protected readonly RegistryInterface $metadataRegistry,
        protected readonly ContextualGridViewFactoryInterface $contextualGridViewFactory,
    ) {
    }

    #[ExposeInTemplate('metadata')]
    public function getMetadata(): MetadataInterface
    {
        return $this->metadataRegistry->get($this->resourceAlias);
    }

    protected function getGridName(): string
    {
        return $this->gridName;
    }

    protected function createGridView(Grid $gridDefinition, Parameters $parameters): ContextualGridView
    {
        return $this->contextualGridViewFactory->create(
            $gridDefinition,
            $parameters,
            new GridContext(metadata: $this->getMetadata(), section: $this->section, variables: $this->gridVariables),
        );
    }
}
