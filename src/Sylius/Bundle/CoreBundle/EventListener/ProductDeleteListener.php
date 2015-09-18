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

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Mateusz Zalewski <mateusz.p.zalewski@gmail.com>
 */
class ProductDeleteListener
{
    /**
     * @var ObjectManager
     */
    private $reviewManager;

    /**
     * @param ObjectManager $reviewManager
     */
    public function __construct(ObjectManager $reviewManager)
    {
        $this->reviewManager = $reviewManager;
    }

    /**
     * @param GenericEvent $event
     */
    public function removeProductReviews(GenericEvent $event)
    {
        if (!(($product = $event->getSubject()) instanceof ProductInterface)) {
            throw new UnexpectedTypeException($product, 'Sylius\Component\Core\Model\ProductInterface');
        }

        $product->setAverageRating(null);
        foreach ($product->getReviews() as $review) {
            $this->reviewManager->remove($review);
        }

        $this->reviewManager->flush();
    }
}
