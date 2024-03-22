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

namespace Sylius\Bundle\AdminBundle\Grid\Renderer;

use Sylius\Bundle\ResourceBundle\Grid\Parser\OptionsParserInterface;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;
use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Renderer\GridRendererInterface as BaseGridRendererInterface;
use Sylius\Component\Grid\View\GridViewInterface;
use Twig\Environment as Twig;
use Webmozart\Assert\Assert;

/** @internal */
final readonly class TwigGridRenderer implements GridRendererInterface
{
    /**
     * @param array<string, string> $itemActionTemplates
     */
    public function __construct(
        private BaseGridRendererInterface $decorated,
        private Twig $twig,
        private OptionsParserInterface $optionsParser,
        private array $itemActionTemplates = [],
    ) {
    }

    public function render(GridViewInterface $gridView, ?string $template = null): string
    {
        return $this->decorated->render($gridView, $template);
    }

    public function renderField(GridViewInterface $gridView, Field $field, $data): string
    {
        return $this->decorated->renderField($gridView, $field, $data);
    }

    public function renderAction(GridViewInterface $gridView, Action $action, $data = null): string
    {
        return $this->decorated->renderAction($gridView, $action, $data);
    }

    public function renderItemAction(GridViewInterface $gridView, Action $action, mixed $data = null): string
    {
        Assert::isInstanceOf($gridView, ResourceGridView::class);

        $type = $action->getType();
        if (!isset($this->itemActionTemplates[$type])) {
            throw new \InvalidArgumentException(sprintf('Missing template for action type "%s".', $type));
        }

        $options = $this->optionsParser->parseOptions(
            $action->getOptions(),
            $gridView->getRequestConfiguration()->getRequest(),
            $data,
        );

        return $this->twig->render($this->itemActionTemplates[$type], [
            'grid' => $gridView,
            'action' => $action,
            'data' => $data,
            'options' => $options,
        ]);
    }

    public function renderFilter(GridViewInterface $gridView, Filter $filter): string
    {
        return $this->decorated->renderFilter($gridView, $filter);
    }
}
