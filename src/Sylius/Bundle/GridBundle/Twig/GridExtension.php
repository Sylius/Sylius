<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\Twig;

use Sylius\Bundle\ResourceBundle\Controller\Configuration;
use Sylius\Bundle\ResourceBundle\Controller\ParametersParser;
use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Definition\Column;
use Sylius\Component\Grid\Renderer\ColumnRendererInterface;
use Sylius\Component\Grid\Sorter\SorterInterface;
use Sylius\Component\Grid\View\GridView;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class GridExtension extends \Twig_Extension
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var ColumnRendererInterface
     */
    private $columnRenderer;

    /**
     * @var ParametersParser
     */
    private $parametersParser;

    /**
     * @param FormFactoryInterface    $formFactory
     * @param ColumnRendererInterface $columnRenderer
     * @param ParmaetersParser        $parametersParser
     */
    public function __construct(FormFactoryInterface $formFactory, ColumnRendererInterface $columnRenderer, ParametersParser $parametersParser)
    {
        $this->formFactory = $formFactory;
        $this->columnRenderer = $columnRenderer;
        $this->parametersParser = $parametersParser;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('sylius_grid_render', array($this, 'render'), array(
                'is_safe'           => array('html'),
                'needs_environment' => true
            )),
            new \Twig_SimpleFunction('sylius_grid_render_header', array($this, 'renderHeader'), array(
                'is_safe'           => array('html'),
                'needs_environment' => true
            )),
            new \Twig_SimpleFunction('sylius_grid_render_value', array($this, 'renderValue'), array(
                'is_safe'           => array('html'),
                'needs_environment' => true
            )),
            new \Twig_SimpleFunction('sylius_grid_render_action', array($this, 'renderAction'), array(
                'is_safe'           => array('html'),
                'needs_environment' => true
            )),
            new \Twig_SimpleFunction('sylius_grid_render_filter_form', array($this, 'renderFilterForm'), array(
                'is_safe'           => array('html'),
                'needs_environment' => true
            )),
        );
    }

    /**
     * @param \Twig_Environment $twig
     * @param GridView          $gridView
     */
    public function render(\Twig_Environment $twig, GridView $gridView, Configuration $configuration)
    {
        return $twig->render('SyliusGridBundle::_grid.html.twig', array('grid' => $gridView, 'configuration' => $configuration));
    }

    /**
     * @param \Twig_Environment $twig
     * @param GridView          $gridView
     * @param Column            $column
     *
     * @return string
     */
    public function renderHeader(\Twig_Environment $twig, GridView $gridView, Column $column)
    {
        $sorting = $gridView->getParameters()->get('sorting', array());

        if (!$column->isSortable()) {
            return $column->getLabel() ?: $column->getField();
        }

        $data = array('column' => $column, 'sorting' => $sorting);
        $name = $column->getName();

        if (isset($sorting[$name]) && in_array($sorting[$name], array(SorterInterface::ASC, SorterInterface::DESC))) {
            $data['direction'] = $sorting[$name];
            $data['sorting'] = array($name => SorterInterface::ASC === $sorting[$name] ? SorterInterface::DESC : SorterInterface::ASC);
        } else {
            $data['sorting'] = array($name => SorterInterface::ASC);
        }

        return $twig->render('SyliusGridBundle::_header.html.twig', $data);
    }

    /**
     * @param \Twig_Environment $twig
     * @param $object
     * @param GridView          $gridView
     * @param $field
     */
    public function renderValue(\Twig_Environment $twig, $object, GridView $gridView, $field)
    {
        return $this->columnRenderer->render($object, $field, $gridView->getDefinition());
    }

    /**
     * @param \Twig_Environment $twig
     * @param GridView          $gridView
     * @param Action            $actionDefinition
     * @param Request           $request
     * @param $object
     */
    public function renderAction(\Twig_Environment $twig, GridView $gridView, Action $actionDefinition, Request $request, $object = null)
    {
        $options = $actionDefinition->getOptions();

        $parameters = isset($options['parameters']) ? $options['parameters'] : array();
        $parameters = $this->parametersParser->parse($parameters, $request);

        if (null !== $object) {
            $parameters = $this->parametersParser->process($parameters, $object);
        }
        // Assign only parameters values
        $contextParameters = $parameters[0];

        $context = array('action' => $actionDefinition, 'parameters' => $contextParameters);

        return $twig->render(sprintf('SyliusGridBundle:Action:_%s.html.twig', $actionDefinition->getType()), $context);
    }

    /**
     * @param \Twig_Environment $twig
     * @param Request           $request
     * @param GridView          $gridView
     */
    public function renderFilterForm(\Twig_Environment $twig, Request $request, GridView $gridView)
    {
        $gridDefinition = $gridView->getDefinition();
        $filterFormBuilder = $this->formFactory->createNamedBuilder('filters', 'form', array(), array('csrf_protection' => false));

        foreach ($gridDefinition->getFilters() as $field => $filterDefinition) {
            $filterFormBuilder->add($field, sprintf('sylius_filter_%s', $filterDefinition->getType()), $filterDefinition->getOptions());
        }

        $form = $filterFormBuilder->getForm();
        $form->submit($request);

        return $twig->render('SyliusGridBundle::_filterForm.html.twig', array('form' => $form->createView(), 'grid' => $gridView));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_grid';
    }
}
