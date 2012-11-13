<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Controller;

use FOS\RestBundle\Util\Pluralization;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Model\ResourceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Base resource controller for Sylius.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class ResourceController extends Controller
{
    protected $bundlePrefix;
    protected $resourceName;
    protected $templateNamespace;

    public function __construct($bundlePrefix, $resourceName, $templateNamespace)
    {
        $this->bundlePrefix = $bundlePrefix;
        $this->resourceName = $resourceName;
        $this->templateNamespace = $templateNamespace;
    }

    /**
     * Get single resource by its identifier.
     */
    public function getAction(Request $request)
    {
        $criteria = $this
            ->getRequestFetcher()
            ->getIdentifierCriteria()
        ;

        $resource = $this->findOr404($criteria);

        $view = View::create()
            ->setTemplate($this->getFullTemplateName('show.html'))
            ->setTemplateVar($this->resourceName)
            ->setData($resource)
        ;

        return $this->handleView($view);
    }

    /**
     * Get collection (paginated by default) of resources.
     */
    public function getCollectionAction(Request $request)
    {
        $fetcher = $this->getRequestFetcher();
        $pluralName = Pluralization::pluralize($this->resourceName);
        $criteria = $fetcher->getCriteria();
        $sorting = $fetcher->getSorting();

        $view = View::create()
            ->setTemplate($this->getFullTemplateName('list.html'))
        ;

        if ($fetcher->isCollectionPaginated()) {
            $paginator = $this
                ->getRepository()
                ->createPaginator($criteria, $sorting)
            ;

            $paginator->setCurrentPage($request->get('page', 1), true, true);
            $paginator->setMaxPerPage($fetcher->getPaginationMaxPerPage());

            $resources = $paginator->getCurrentPageResults();

            $data = $fetcher->isHtmlRequest() ? array(
                $pluralName => $resources,
                'paginator' => $paginator
            ) : $resources;
        } else {
            $view->setTemplateVar($pluralName);

            $data = $this
                ->getRepository()
                ->findBy($criteria, $sorting, $fetcher->getLimit())
            ;
        }

        $view->setData($data);

        return $this->handleView($view);
    }

    /**
     * Create new resource or just display the form.
     */
    public function createAction(Request $request)
    {
        $resource = $this->create();
        $form = $this->getForm($resource);

        if ($request->isMethod('POST') && $form->bind($request)->isValid()) {
            $this->getManager()->persist($resource);

            return $this->redirectTo($resource);
        }

        if (!$this->getRequestFetcher()->isHtmlRequest()) {
            return $this->handleView(View::create($form));
        }

        $view = View::create()
            ->setTemplate($this->getFullTemplateName('create.html'))
            ->setData(array(
                $this->resourceName => $resource,
                'form'              => $form->createView()
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * Display the form for editing or update the resource.
     */
    public function updateAction(Request $request)
    {
        $criteria = $this
            ->getRequestFetcher()
            ->getIdentifierCriteria()
        ;

        $resource = $this->findOr404($criteria);
        $form = $this->getForm($resource);

        if ($request->isMethod('POST') && $form->bind($request)->isValid()) {
            $this->getManager()->persist($resource);

            return $this->redirectTo($resource);
        }

        if (!$this->getRequestFetcher()->isHtmlRequest()) {
            return $this->handleView(View::create($form));
        }

        $view = View::create()
            ->setTemplate($this->getFullTemplateName('create.html'))
            ->setData(array(
                $this->resourceName => $resource,
                'form'              => $form->createView()
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * Delete resource.
     */
    public function deleteAction(Request $request)
    {
        $criteria = $this
            ->getRequestFetcher()
            ->getIdentifierCriteria()
        ;

        $resource = $this->findOr404($criteria);
        $this->getManager()->remove($resource);

        return $this->redirectToCollection();
    }

    protected function getRequestFetcher()
    {
        return $this->get('sylius_resource.fetcher');
    }

    protected function create()
    {
        return $this->getManager()->create();
    }

    protected function getForm(ResourceInterface $resource = null)
    {
        return $this->createForm($this->getFormType(), $resource);
    }

    protected function getFormType()
    {
        if (null !== $type = $this->getRequestFetcher()->getFormType()) {
            return $type;
        }

        return sprintf('%s_%s', $this->bundlePrefix, $this->resourceName);
    }

    protected function redirectTo(ResourceInterface $resource)
    {
        $redirect = $this->getRequestFetcher()->getRedirect();
        $route = $redirect ? $redirect : $this->getResourceRoute();

        return $this->handleView(RouteRedirectView::create($route, array('id' => $resource->getId())));
    }

    protected function redirectToCollection()
    {
        $redirect = $this->getRequestFetcher()->getRedirect();
        $route = $redirect ? $redirect : $this->getResourceCollectionRoute();

        return $this->handleView(RouteRedirectView::create($route));
    }

    protected function getRoute()
    {
        return sprintf('%s_%s', $this->bundlePrefix, $this->resourceName);
    }

    protected function getCollectionRoute()
    {
        return sprintf('%s_%s', $this->bundlePrefix, Pluralization::pluralize($this->resourceName));
    }

    protected function getManager()
    {
        return $this->getService('manager');
    }

    protected function getRepository()
    {
        return $this->getService('repository');
    }

    protected function getService($name)
    {
        return $this->get($this->getFullServiceName($name));
    }

    protected function getFullServiceName($name)
    {
        return sprintf('%s.%s.%s', $this->bundlePrefix, $name, $this->resourceName);
    }

    protected function findOr404(array $criteria)
    {
        if (!$resource = $this->getRepository()->findOneBy($criteria)) {
            throw new NotFoundHttpException('Requested resource does not exist');
        }

        return $resource;
    }

    protected function renderResponse($templateName, array $parameters = array())
    {
        return $this->render($this->getFullTemplateName($templateName), $parameters);
    }

    protected function getFullTemplateName($name)
    {
        if (null !== $template = $this->getRequestFetcher()->getTemplate()) {
            return $template;
        }

        return sprintf('%s:%s.%s',
            $this->templateNamespace,
            $name,
            $this->getEngine()
        );
    }

    protected function getEngine()
    {
        return $this->container->getParameter(sprintf('%s.engine', $this->bundlePrefix));
    }

    protected function handleView(View $view)
    {
        return $this->get('fos_rest.view_handler')->handle($view);
    }

    protected function setFlash($type, $message)
    {
        return $this
            ->get('session')
            ->getFlashBag()
            ->set($type, $message)
        ;
    }
}
