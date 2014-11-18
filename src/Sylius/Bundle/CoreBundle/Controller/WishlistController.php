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

use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Core\Model\WishlistInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WishlistController extends ResourceController
{
    public function setAsPublicAction(Request $request, $id)
    {
        /** @var $wishlist WishlistInterface */
        if (!$product = $this->getWishlistRepository()->findOneBy(array('id' => $id, 'user' => $this->getUser()))) {
            throw new NotFoundHttpException();
        }

        $wishlist = $this->findOr404($request);
        $wishlist->setPublic($request->request->get('public', false));

        $this->domainManager->update($wishlist);

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($wishlist, 204));
        }

        return $this->redirectHandler->redirectTo($wishlist);
    }

    public function manageProduct(Request $request, $action, $productId)
    {
        if ('add' !== $action && 'remove' !== $action) {
            throw new NotFoundHttpException();
        }

        /** @var $wishlist WishlistInterface */
        $wishlist = $this->findOr404($request);

        if (!$product = $this->getProductVariantRepository()->find($productId)) {
            throw new NotFoundHttpException();
        }

        $item = $this->getWishlistItemRepository()->createNew();
        $item->setProduct($product);

        $wishlist->{$action.'Item'}($item);

        if ('remove' === $action) {
            $this->domainManager->delete($item);
        }

        $this->domainManager->update($wishlist);

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($wishlist, 204));
        }

        return $this->redirectHandler->redirectTo($wishlist);
    }

    /**
     * @return ProductRepository
     */
    public function getProductVariantRepository()
    {
        return $this->get('sylius.repository.product_variant');
    }

    /**
     * @return RepositoryInterface
     */
    public function getWishlistRepository()
    {
        return $this->get('sylius.repository.wishlist');
    }

    /**
     * @return RepositoryInterface
     */
    public function getWishlistItemRepository()
    {
        return $this->get('sylius.repository.wishlist_item');
    }
}
