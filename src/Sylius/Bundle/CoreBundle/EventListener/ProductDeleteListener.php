<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class ProductDeleteListener
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postSoftDelete(LifecycleEventArgs $args)
    {
        if (!($product = $args->getEntity()) instanceof ProductInterface) {
            return;
        }

        $reviewManager = $args->getEntityManager();
        $reviewRepository = $this->container->get('sylius.repository.product_review');
        $reviews = $reviewRepository->findBy(['reviewSubject' => $product]);

        foreach ($reviews as $review) {
            $reviewManager->remove($review);
        }

        $product->setAverageRating(null);

        $reviewManager->flush();
    }
}
