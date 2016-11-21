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
use Sylius\Component\Grid\FieldTypes\FieldTypeInterface;
use Sylius\Component\Grid\Renderer\GridRendererInterface;
use Sylius\Component\Grid\View\GridView;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class TwigGridRenderer implements GridRendererInterface
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
     * {@inheritdoc}
     */
    public function renderField(GridView $gridView, Field $field, $data)
    {
        /** @var FieldTypeInterface $fieldType */
        $fieldType = $this->fieldsRegistry->get($field->getType());
        $resolver = new OptionsResolver();
        $fieldType->configureOptions($resolver);
        $options = $resolver->resolve($field->getOptions());

        return $fieldType->render($field, $data, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function renderAction(GridView $gridView, Action $action, $data = null)
    {
        $type = $action->getType();
        if (!isset($this->actionTemplates[$type])) {
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
        $template = $this->getFilterTemplate($filter);

        $form = $this->formFactory->createNamed('criteria', 'form', [], ['csrf_protection' => false, 'required' => false]);
        $form->add($filter->getName(), sprintf('sylius_grid_filter_%s', $filter->getType()), $filter->getOptions());

        $criteria = $gridView->getParameters()->get('criteria', []);
        $form->submit($criteria);

        return $this->twig->render($template, [
            'grid' => $gridView,
            'filter' => $filter,
            'form' => $form->get($filter->getName())->createView(),
        ]);
    }

    /**
     * @param Filter $filter
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    private function getFilterTemplate(Filter $filter)
    {
        $template = $filter->getTemplate();
        if (null !== $template) {
            return $template;
        }

        $type = $filter->getType();
        if (!isset($this->filterTemplates[$type])) {
            throw new \InvalidArgumentException(sprintf('Missing template for filter type "%s".', $type));
        }

        return $this->filterTemplates[$type];
    }
}
