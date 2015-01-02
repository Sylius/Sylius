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

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Variation\Generator\VariantGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Product controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class ProductController extends ResourceController
{
    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        $product = parent::createNew();

        $code = $this->getRequest()->query->get('archetype');

        if (null === $code) {
            return $product;
        }

        $archetype = $this->getArchetypeRepository()->findOneBy(array('code' => $code));

        if (!$archetype) {
            throw new NotFoundHttpException(sprintf('Requested archetype does not exist!'));
        }

        $this
            ->getBuilder()
            ->build($archetype, $product)
        ;

        return $product;
    }

    /**
     * Get archetype repository.
     *
     * @return ObjectRepository
     */
    protected function getArchetypeRepository()
    {
        return $this->get('sylius.repository.product_archetype');
    }

    /**
     * Get archetype builder.
     *
     * @return ArchetypeBuilderInterface
     */
    protected function getBuilder()
    {
        return $this->get('sylius.builder.product_archetype');
    }
}
