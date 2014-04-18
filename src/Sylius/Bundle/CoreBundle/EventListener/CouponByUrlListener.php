<?php
namespace Sylius\Bundle\CoreBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sylius\Bundle\CartBundle\Event\CartEvent;

class CouponByUrlListener
{
    private $queryName;
    private $cartManager;
    private $couponRepository;
    private $session;

    public function __construct(RepositoryInterface $couponRepository, SessionInterface $session, $queryName = 'promotionCoupon')
    {
        $this->couponRepository = $couponRepository;
        $this->session = $session;
        $this->queryName = $queryName;
    }

    public function applySessionCouponToCart(CartEvent $event)
    {
        $coupon = $this->couponRepository->findOneBy(array('code' => $this->session->get('coupon_store_' . $this->queryName)));

        if ($coupon && $event->getCart() instanceof PromotionSubjectInterface) {
            $cart = $event->getCart();
            $cart->setPromotionCoupon($coupon);
        }
    }

    public function applyRequestToSessionCoupon(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (null !== $code = $request->query->get($this->queryName)) {
            $this->session->set('coupon_store_' . $this->queryName, $code);
        }
    }
}
