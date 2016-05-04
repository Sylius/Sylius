<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\Renderer;

use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Renderer\GridRendererInterface;
use Sylius\Component\Grid\View\GridView;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class TwigGridRenderer implements GridRendererInterface
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var string
     */
    private $defaultTemplate;

    /**
     * @var ServiceRegistryInterface
     */
    private $fieldsRegistry;

    /**
     * @var array
     */
    private $actionTemplates;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var array
     */
    private $filterTemplates;

    /**
     * @param \Twig_Environment $twig
     * @param ServiceRegistryInterface $fieldsRegistry
     * @param FormFactoryInterface $formFactory
     * @param string $defaultTemplate
     * @param array $actionTemplates
     * @param array $filterTemplates
     */
    public function __construct(
        \Twig_Environment $twig,
        ServiceRegistryInterface $fieldsRegistry,
        FormFactoryInterface $formFactory,
        $defaultTemplate,
        array $actionTemplates = [],
        array $filterTemplates = []
    ) {
        $this->twig = $twig;
        $this->defaultTemplate = $defaultTemplate;
        $this->fieldsRegistry = $fieldsRegistry;
        $this->actionTemplates = $actionTemplates;
        $this->formFactory = $formFactory;
        $this->filterTemplates = $filterTemplates;
    }

    /**
     * {@inheritdoc}
     */
    public function render(GridView $gridView, $template = null)
    {
        return $this->twig->render($template ?: $this->defaultTemplate, ['grid' => $gridView]);
    }

    /**
     * @param Field $field
     * @param $data
     */
    public function renderField(GridView $gridView, Field $field, $data)
    {
        return $this->fieldsRegistry->get($field->getType())->render($field, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function renderAction(GridView $gridView, Action $action, $data = null)
    {
        if (!isset($this->actionTemplates[$type = $action->getType()])) {
            throw new \InvalidArgumentException(sprintf('Missing template for action type "%s".', $type));
        }

        return $this->twig->render($this->actionTemplates[$type], [
            'grid' => $gridView,
            'action' => $action,
            'data' => $data,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function renderFilter(GridView $gridView, Filter $filter)
    {
        if (!isset($this->filterTemplates[$type = $filter->getType()])) {
            throw new \InvalidArgumentException(sprintf('Missing template for filter type "%s".', $type));
        }

        $criteria = $gridView->getParameters()->get('criteria', []);

        $form = $this->formFactory->createNamed('criteria', 'form', $criteria, ['csrf_protection' => false, 'required' => false]);
        $form->add($filter->getName(), sprintf('sylius_grid_filter_%s', $filter->getType()), $filter->getOptions());

        return $this->twig->render($this->filterTemplates[$type], [
            'grid' => $gridView,
            'filter' => $filter,
            'form' => $form->get($filter->getName())->createView(),
        ]);
    }
}
