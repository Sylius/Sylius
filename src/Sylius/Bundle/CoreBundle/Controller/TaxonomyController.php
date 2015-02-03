<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Product controller.
 *
 * @author Matthias Esterl <inventor@madcity.at>
 */
class TaxonomyController extends ResourceController
{
    /**
     * Move a taxon up in the tree.
     *
     * @param integer $id
     *
     * @return RedirectResponse
     *
     * @throws NotFoundHttpException
     */
    public function moveTaxonUpAction($id)
    {
        $taxonRepository = $this->get('sylius.repository.taxon');
        $taxon = $taxonRepository->find($id);

        if (!isset($taxon)) {
            throw new NotFoundHttpException('Requested taxon does not exist.');
        }

        $taxonRepository->moveUp($taxon);
        
        return $this->redirectHandler->redirectToReferer();
    }
    
    /**
     * Move a taxon down in the tree.
     *
     * @param integer $id
     *
     * @return RedirectResponse
     *
     * @throws NotFoundHttpException
     */
    public function moveTaxonDownAction($id)
    {
        $taxonRepository = $this->get('sylius.repository.taxon');
        $taxon = $taxonRepository->find($id);

        if (!isset($taxon)) {
            throw new NotFoundHttpException('Requested taxon does not exist.');
        }

        $taxonRepository->moveDown($taxon);
        
        return $this->redirectHandler->redirectToReferer();
    }
}
