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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sylius\Component\Rbac\Model\RoleInterface;
use Sylius\Bundle\StoreBundle\Doctrine\ORM\StoreRepository;
use FOS\RestBundle\View\View;
use Hateoas\Configuration\Route;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Sylius\Bundle\ResourceBundle\Form\DefaultFormFactory;
use Sylius\Component\Resource\Event\ResourceEvent;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Product controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class ProductController extends ResourceController
{

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $this->isGrantedOr403('index');

        $criteria = $this->config->getCriteria();
        $sorting = $this->config->getSorting();

        $repository = $this->getRepository();


        $user = $this->getUser();
        if ($user) {
            if ($roles = $user->getAuthorizationRoles()) {
                if ($roles[0]->getCode() == 'store_owner') {
                    $storeRepository = $this->container->get('sylius.repository.store');
                    $store = $storeRepository->findOneBy(array('user' => $user->getId()));
                    $criteria = array('store' => $store->getId());
                }
            }
        }


        $resources = $repository->findBy($criteria);

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('index.html'))
            ->setTemplateVar($this->config->getPluralResourceName())
            ->setData($resources);

        return $this->handleView($view);


    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        $product = parent::createNew();

        $code = $this->getRequest()->query->get('archetype');

        $user = $this->getUser();
        $roles = $user->getAuthorizationRoles()[0]->getCode();

        if ($roles == 'store_owner') {
            $repository = $this->container->get('sylius.repository.store');
            $store = $repository->findOneBy(array('user' => $user->getId()));
            $product->setStore($store);
        }

        if (null === $code) {
            return $product;
        }

        $archetype = $this->getArchetypeRepository()->findOneBy(array('code' => $code));

        if (!$archetype) {
            throw new NotFoundHttpException(sprintf('Requested archetype does not exist!'));
        }

        $product->setArchetype($archetype);


        $this
            ->getBuilder()
            ->build($product);


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
     * Get archetype repository.
     *
     * @return ObjectRepository
     */
    protected function getStoreRepository()
    {
        return $this->get('sylius.repository.store');
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
