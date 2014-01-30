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

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Taxon controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class TaxonController extends ResourceController
{
    /**
     * {@inheritdoc}
     */
    protected function createNew(Request $request)
    {
        if (null === $taxonomyId = $request->get('taxonomyId')) {
            throw new NotFoundHttpException('No taxonomy id given.');
        }

        if (!$taxonomy = $this->getTaxonomyRepository()->find($taxonomyId)) {
            throw new NotFoundHttpException('Requested taxonomy does not exist.');
        }

        $taxon = parent::createNew($request);
        $taxon->setTaxonomy($taxonomy);

        return $taxon;
    }

    /**
     * Get taxonomy repository.
     *
     * @return ObjectRepository
     */
    protected function getTaxonomyRepository()
    {
        return $this->get('sylius.repository.taxonomy');
    }
}
