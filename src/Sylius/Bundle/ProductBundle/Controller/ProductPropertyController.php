<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductPropertyController extends ResourceController
{
    /**
     * {@inheritdoc}
     */
    public function createAction(Request $request)
    {
        $property = $this->getProperty($request->get('id'));
        $position = $request->get('position');

        /** @var \Sylius\Bundle\ProductBundle\Model\ProductProperty $productProperty */
        $productProperty = $this->createNew();
        $productProperty->setProperty($property);

        $form = $this->createForm('sylius_property_collection', array($position => $productProperty));
        $template = $this->getConfiguration()->getTemplate('render.html');

        $parameters = array_merge(
            array('form' => $form->createView()),
            $form->getConfig()->getOptions()
        );

        return $this->render($template, $parameters);
    }

    /**
     * @param  integer|null                                                  $id
     * @return integer
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getProperty($id)
    {
        $property = $this->get('sylius.repository.property')->find($id);

        if (null === $property) {
            throw new NotFoundHttpException('Requested property does not exist');
        }

        return $property;
    }
}
