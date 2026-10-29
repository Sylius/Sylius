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

use Pagerfanta\PagerfantaInterface;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Symfony\Form\Registry\FormTypeRegistryInterface;
use Sylius\Component\Grid\Twig\Component\ComponentWithGridTrait;
use Sylius\Resource\Grid\View\ContextualGridView;
use Sylius\Resource\Grid\View\Factory\ContextualGridViewFactoryInterface;
use Sylius\Resource\Grid\View\GridContext;
use Sylius\Resource\Metadata\MetadataInterface;
use Sylius\Resource\Metadata\RegistryInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\PreReRender;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PostMount;

/**
 * Generic live grid of the admin index pages.
 *
 * The resource controller builds the grid from the current request, which is not available anymore during
 * live re-renders. The grid is therefore built from a grid context: the route parameters of the index page
 * are kept as its variables, so that the grid expressions ("$productId", "resource.id"...) keep working,
 * and the action templates guess their routes with "grid.gridContext.routeName(...)".
 *
 * @experimental
 */
class GridComponent
{
    use ComponentWithGridTrait;
    use DefaultActionTrait;
    use HookableLiveComponentTrait;
    use TemplatePropTrait;

    /** Same default as RequestConfiguration::getPaginationMaxPerPage() */
    private const DEFAULT_MAX_PER_PAGE = 10;

    #[LiveProp]
    public string $resourceAlias;

    #[LiveProp]
    public string $gridName;

    #[LiveProp]
    public string $section = 'admin';

    /**
     * The route parameters of the index page (e.g. "productId"), used as the grid context variables.
     *
     * @var array<string, mixed>|null
     */
    #[LiveProp]
    public ?array $routeParameters = null;

    private ?ContextualGridView $gridView = null;

    public function __construct(
        protected readonly RegistryInterface $metadataRegistry,
        protected readonly ContextualGridViewFactoryInterface $contextualGridViewFactory,
        protected readonly RequestStack $requestStack,
        protected readonly FormFactoryInterface $formFactory,
        protected readonly FormTypeRegistryInterface $filterFormTypeRegistry,
    ) {
    }

    /** Keeps the route parameters of the index page, as they are not available anymore during live re-renders. */
    #[PostMount]
    public function captureRouteParameters(): void
    {
        $this->routeParameters ??= $this->requestStack->getCurrentRequest()?->attributes->all('_route_params') ?? [];
    }

    /**
     * The filters form fields are bound to the "criteria" prop through their name (e.g. "criteria[search][value]"),
     * but the live component JS cannot write a nested value if its parents do not exist. The criteria are therefore
     * initialized with the empty values of the filters form, as ComponentWithFormTrait does with its "formValues".
     */
    #[PostMount(priority: -10)]
    #[PreReRender]
    public function initializeCriteria(): void
    {
        $this->criteria = array_replace_recursive($this->getEmptyCriteria(), $this->criteria ?? []);
    }

    public function getGridView(): ContextualGridView
    {
        // The grid is exposed several times per render (filters, data table, no results...), build it only once
        if (null !== $this->gridView) {
            return $this->gridView;
        }

        $gridView = $this->contextualGridViewFactory->create(
            $this->getGridDefinition(),
            new Parameters($this->getGridConfig()),
            new GridContext(metadata: $this->getMetadata(), section: $this->section, variables: $this->routeParameters ?? []),
        );

        // Same pagination as the ResourcesCollectionProvider of the resource controller
        $data = $gridView->getData();
        if ($data instanceof PagerfantaInterface) {
            $data->setMaxPerPage($this->resolveMaxPerPage($gridView->getDefinition()->getLimits()));
            $data->setCurrentPage(max(1, $this->page ?? 1));
            $data->getCurrentPageResults();
        }

        return $this->gridView = $gridView;
    }

    #[ExposeInTemplate('metadata')]
    public function getMetadata(): MetadataInterface
    {
        return $this->metadataRegistry->get($this->resourceAlias);
    }

    /** The criteria always contain the (empty) values of all the filters, see initializeCriteria() */
    public function hasActiveCriteria(): bool
    {
        $hasValue = static function (mixed $value) use (&$hasValue): bool {
            if (is_array($value)) {
                return [] !== array_filter($value, $hasValue);
            }

            return null !== $value && '' !== $value;
        };

        return $hasValue($this->criteria ?? []);
    }

    /** Applies the criteria bound to the filters form (see the "data-model" attribute of the form). */
    #[LiveAction]
    public function applyFilters(): void
    {
        $this->page = 1;
    }

    protected function getGridName(): string
    {
        return $this->gridName;
    }

    /** @return array<string, mixed> */
    private function getEmptyCriteria(): array
    {
        // Same form as the one built by the TwigGridRenderer to render each filter
        $form = $this->formFactory->createNamed('criteria', FormType::class, null, [
            'allow_extra_fields' => true,
            'csrf_protection' => false,
            'required' => false,
        ]);

        foreach ($this->getGridDefinition()->getEnabledFilters() as $filter) {
            $form->add($filter->getName(), $this->filterFormTypeRegistry->get($filter->getType(), 'default'), $filter->getFormOptions());
        }

        return $this->extractFormValues($form->createView());
    }

    /**
     * Simplified version of ComponentWithFormTrait::extractFormValues().
     *
     * @return array<string, mixed>
     */
    private function extractFormValues(FormView $formView): array
    {
        $values = [];

        foreach ($formView->children as $name => $child) {
            $isCompound = $child->vars['compound_data'] ?? $child->vars['compound'] ?? false;

            if ($isCompound && !($child->vars['expanded'] ?? false)) {
                $values[$name] = $this->extractFormValues($child);

                continue;
            }

            if (array_key_exists('checked', $child->vars)) {
                $values[$name] = $child->vars['checked'] ? $child->vars['value'] : null;

                continue;
            }

            $values[$name] = $child->vars['value'];
        }

        return $values;
    }

    /** @param int[] $gridLimits */
    private function resolveMaxPerPage(array $gridLimits): int
    {
        if (null === $this->limit) {
            return reset($gridLimits) ?: self::DEFAULT_MAX_PER_PAGE;
        }

        if ([] !== $gridLimits) {
            return min($this->limit, max($gridLimits));
        }

        return $this->limit;
    }
}
