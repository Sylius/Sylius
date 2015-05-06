<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\RouterInterface;

/**
 * Symfony form type for easy autocomplete fields for Sylius resources.
 *
 * @author Pawęł Jędrzejewski <pawel@sylius.org>
 */
class ResourceAutocompleteType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * Form name.
     *
     * @var string
     */
    protected $name;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var string
     */
    protected $suggestionsRoute;

    /**
     * @param string $name
     * @param RepositoryInterface $repository
     * @param RouterInterface     $router
     * @param string              $suggestionsRoute
     */
    public function __construct($name, RepositoryInterface $repository, RouterInterface $router, $suggestionsRoute)
    {
        $this->name = $name;
        $this->repository = $repository;
        $this->router = $router;
        $this->suggestionsRoute = $suggestionsRoute;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addModelTransformer(new ResourceToIdentifierTransformer($this->repository))
            ->add('identifier', 'hidden', array(
                'mapped' => false
            ))
            ->add('input', 'text', array(
                'mapped'   => false,
                'required' => true,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['url'] = $this->router->generate($this->suggestionsRoute, array(), true);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
}
