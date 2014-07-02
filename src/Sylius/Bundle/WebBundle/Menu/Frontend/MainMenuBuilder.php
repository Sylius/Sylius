<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Menu\Frontend;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sylius\Bundle\MoneyBundle\Twig\MoneyExtension;
use Sylius\Bundle\UiBundle\Menu\MenuBuilder;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Frontend main menu builder.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class MainMenuBuilder extends MenuBuilder
{
    /**
     * Taxonomy repository.
     *
     * @var RepositoryInterface
     */
    protected $taxonomyRepository;

    /**
     * Constructor.
     *
     * @param FactoryInterface          $factory
     * @param EventDispatcherInterface  $eventDispatcher
     * @param RepositoryInterface       $taxonomyRepository
     */
    public function __construct(
        FactoryInterface          $factory,
        EventDispatcherInterface  $eventDispatcher,
        RepositoryInterface       $taxonomyRepository
    )
    {
        parent::__construct($factory, $eventDispatcher);

        $this->taxonomyRepository = $taxonomyRepository;
    }

    /**
     * Builds frontend menu from taxonomies.
     *
     * @return ItemInterface
     */
    public function createMenu()
    {
        $menu = $this->factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav'
            )
        ));

        $childOptions = array(
            'childrenAttributes' => array('class' => 'nav nav-list'),
            'labelAttributes'    => array('class' => 'nav-header'),
        );

        $taxonomies = $this->taxonomyRepository->findAll();

        foreach ($taxonomies as $taxonomy) {
            $child = $menu->addChild($taxonomy->getName(), $childOptions);

            if ($taxonomy->getRoot()->hasPath()) {
                $child->setLabelAttribute('data-image', $taxonomy->getRoot()->getPath());
            }

            $this->createTaxonomiesMenuNode($child, $taxonomy->getRoot());
        }

        return $menu;
    }

    private function createTaxonomiesMenuNode(ItemInterface $menu, TaxonInterface $taxon)
    {
        foreach ($taxon->getChildren() as $child) {
            $childMenu = $menu->addChild($child->getName(), array(
                'route'           => $child,
                'labelAttributes' => array('icon' => 'icon-angle-right')
            ));
            if ($child->getPath()) {
                $childMenu->setLabelAttribute('data-image', $child->getPath());
            }

            $this->createTaxonomiesMenuNode($childMenu, $child);
        }
    }

}
