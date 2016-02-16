<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class ProductDeleteListenerSpec extends ObjectBehavior
{
    function let(ContainerInterface $container)
    {
        $this->beConstructedWith($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\ProductDeleteListener');
    }

    function it_removes_soft_deleted_product_reviews(
        $container,
        EntityRepository $reviewRepository,
        ObjectManager $reviewManager,
        LifecycleEventArgs $args,
        Product $product,
        ReviewInterface $review
    ) {
        $args->getEntity()->willReturn($product)->shouldBeCalled();

        $container->get('sylius.manager.product_review')->willReturn($reviewManager)->shouldBeCalled();
        $container->get('sylius.repository.product_review')->willReturn($reviewRepository)->shouldBeCalled();

        $reviewRepository->findBy(['reviewSubject' => $product])->willReturn([$review])->shouldBeCalled();

        $reviewManager->remove($review)->shouldBeCalled();
        $reviewManager->flush()->shouldBeCalled();

        $product->setAverageRating(0)->shouldBeCalled();

        $this->postSoftDelete($args);
    }
}
