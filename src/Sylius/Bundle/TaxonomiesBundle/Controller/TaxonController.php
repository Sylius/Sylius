<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxonomiesBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Taxon controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class TaxonController extends ResourceController
{
    public function createNew()
    {
        if (null === $taxonomyId = $this->getRequest()->get('taxonomyId')) {
            throw new NotFoundHttpException('No taxonomy given');
        }

        $taxonomy = $this
            ->getTaxonomyController()
            ->findOr404(array('id' => $taxonomyId))
        ;

        $taxon = parent::createNew();
        $taxon->setTaxonomy($taxonomy);

        return $taxon;
    }

    protected function getTaxonomyController()
    {
        return $this->get('sylius_taxonomies.controller.taxonomy');
    }
}
