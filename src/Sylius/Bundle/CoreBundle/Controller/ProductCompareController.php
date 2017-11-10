<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\CoreBundle\Product\Comparator\ProductComparatorInterface;
use Sylius\Component\Core\Model\Product;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductCompareController
{
    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var ProductComparatorInterface
     */
    private $comparator;

    /**
     * @param EngineInterface $templatingEngine
     */
    public function __construct(
        EngineInterface $templatingEngine,
        EntityManagerInterface $manager,
        ProductComparatorInterface $comparator
    ) {
        $this->templatingEngine = $templatingEngine;
        $this->manager = $manager;
        $this->comparator = $comparator;
    }

    public function indexAction(Request $request): Response
    {
        $productsToCompare = $request->query->get('product');
        $products = $this->manager
            ->getRepository(Product::class)
            ->findBy([
                'id' => $productsToCompare,
            ])
        ;
        $comparedAttributes = $this->comparator->compare($products);

        foreach ($comparedAttributes as $attribute) {
            dump($attribute);
        }
        die;
        return $this->templatingEngine->renderResponse('@SyliusShop/ProductCompare/index.html.twig', [
            'products' => $this->comparator->compare($products),
        ]);
    }
}
