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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Base resource controller for Sylius.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class ResourceController extends Controller implements ResourceControllerInterface
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
     * Get single resource.
     */
    public function getAction(Request $request)
    {
        $criteria = $this->getCriteria();
        $criteria[$this->getIdentifierName()] = $this->getIdentifierValue();

        $resource = $this->findResourceOr404($criteria);

        $view = View::create()
            ->setTemplate($this->getFullTemplateName('show.html'))
            ->setTemplateVar($this->getResourceName())
            ->setData($resource)
        ;

        return $this->handleView($view);
    }

    /**
     * Get collection (paginated by default) of resources.
     */
    public function getCollectionAction(Request $request)
    {
        $criteria = $this->getCriteria();
        $sorting = $this->getSorting();

        if ($this->isPaginated()) {
            $paginator = $this
                ->getRepository()
                ->paginate($criteria, $sorting)
            ;

            $paginator->setCurrentPage($request->query->get('page', 1), true, true);
            $resources = $paginator->getCurrentPageResults();

            $pluralName = Pluralization::pluralize($this->getResourceName());

            $data = $this->isHtmlRequest() ? array(
                $pluralName => $resources,
                'paginator' => $paginator
            ) : $resources;
        } else {
            $data = $this
                ->getRepository()
                ->getCollection($criteria, $sorting)
            ;
        }

        $view = View::create()
            ->setTemplate($this->getFullTemplateName('list.html'))
            ->setData($data)
        ;

        return $this->handleView($view);
    }

    /**
     * Create resource.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $resource = $this->createResource();
        $form = $this->createResourceForm($resource);

        if ($request->isMethod('POST') && $form->bind($request)->isValid()) {
            $this->getManager()->persist($resource);

            return $this->redirectToResource($resource);
        }

        if (!$this->isHtmlRequest()) {
            return $this->handleView(View::create($form));
        }

        $view = View::create()
            ->setTemplate($this->getFullTemplateName('create.html'))
            ->setData(array(
                $this->getResourceName() => $resource,
                'form'                   => $form->createView()
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * Update resource.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function updateAction(Request $request)
    {
        $resource = $this->findResourceOr404($request->get('id'));
        $form = $this->createResourceForm($resource);

        if ($request->isMethod('POST') && $form->bind($request)->isValid()) {
            $this->getManager()->persist($resource);
            $this->setFlash('success', sprintf("%s has been updated", ucfirst($this->getResourceName())));

            return $this->redirectToResource($resource);
        }

        if (!$this->isHtmlRequest()) {
            return $this->handleView(View::create($form));
        }

        $view = View::create()
            ->setTemplate($this->getFullTemplateName('create.html'))
            ->setData(array(
                $this->getResourceName() => $resource,
                'form'                   => $form->createView()
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * Deletes resource.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function deleteAction(Request $request)
    {
        $resource = $this->findResourceOr404($request->get('id'));

        $this->getManager()->remove($resource);
        $this->setFlash('success', sprintf("%s has been deleted", ucfirst($this->getResourceName())));

        return $this->redirectToResourceCollection();
    }

    public function setFlash($name, $value)
    {
        if ($this->isHtmlRequest()) {
            $this
                ->get('session')
                ->getFlashBag()
                ->set($name, $value)
            ;
        }
    }

    /**
     * Create new resource instance.
     */
    protected function createResource()
    {
        return $this->getManager()->create();
    }

    /**
     * Create resource form.
     *
     * @param ResourceInterface $resource
     *
     * @return FormInterface
     */
    protected function createResourceForm(ResourceInterface $resource = null)
    {
        return $this->createForm($this->getResourceFormType(), $resource);
    }

    /**
     * Redirect to resource resource.
     *
     * @param ResourceInterface $resource
     *
     * @return RouteRedirectView
     */
    protected function redirectToResource(ResourceInterface $resource)
    {
        $redirect = $this->getRequest()->attributes->get('_sylius.redirect');
        $route = $redirect ? $redirect : $this->getResourceRoute();

        return $this->handleView(RouteRedirectView::create($route, array('id' => $resource->getId())));
    }

    /**
     * Redirect to list of resourcees.
     *
     * @return RouteRedirectView
     */
    protected function redirectToResourceCollection()
    {
        $redirect = $this->getRequest()->attributes->get('_sylius.redirect');
        $route = $redirect ? $redirect : $this->getResourceCollectionRoute();

        return $this->handleView(RouteRedirectView::create($route));
    }

    protected function getResourceRoute()
    {
        throw new \BadMethodCallException('You have to implement this method');
    }

    protected function getResourceCollectionRoute()
    {
        throw new \BadMethodCallException('You have to implement this method');
    }

    /**
     * Get resource manager.
     *
     * @return ResourceManagerInterface
     */
    protected function getManager()
    {
        return $this->get($this->getServiceName('manager'));
    }

    /**
     * Get resource repository.
     *
     * @return ResourceRepositoryInterface
     */
    protected function getRepository()
    {
        return $this->get($this->getServiceName('repository'));
    }

    protected function getServiceName($name)
    {
        return sprintf('%s.%s.%s', $this->getBundlePrefix(), $name, $this->getResourceName());
    }

    /**
     * Tries to find resource with given id.
     * Throws special 404 exception when unsuccessful.
     *
     * @param array $criteria Criteria
     *
     * @return ResourceInterface
     *
     * @throws NotFoundHttpException
     */
    protected function findResourceOr404(array $criteria)
    {
        if (!$resource = $this->getRepository()->get($criteria)) {
            throw new NotFoundHttpException('Requested resource does not exist');
        }

        return $resource;
    }

    protected function getIdentifierName()
    {
        return $this->getRequest()->attributes->get('_sylius.identifier', 'id');
    }

    protected function getIdentifierValue()
    {
        if (null === $identifier = $this->getRequest()->get($this->getIdentifierName())) {
            throw new NotFoundHttpException('No resource identifier supplied');
        }

        return $identifier;
    }

    protected function isPaginated()
    {
        return (Boolean) $this->getRequest()->attributes->get('_sylius.paginate', true);
    }

    protected function getCriteria()
    {
        return $this->getRequest()->get('_sylius.criteria', array());
    }

    protected function getSorting()
    {
        return $this->getRequest()->get('_sylius.sorting', array());
    }

    protected function renderResponse($templateName, array $parameters = array())
    {
        return $this->render($this->getFullTemplateName($templateName), $parameters);
    }

    protected function getFullTemplateName($name)
    {
        $template = $this->getRequest()->attributes->get('_sylius.template');

        if (null !== $template) {
            return $template;
        }

        return sprintf('%s:%s.%s',
            $this->getTemplateNamespace(),
            $name,
            $this->getEngine()
        );
    }

    protected function getEngine()
    {
        return $this->container->getParameter(sprintf('%s.engine', $this->getBundlePrefix()));
    }

    protected function isHtmlRequest()
    {
        return 'html' === $this->getRequest()->getRequestFormat();
    }

    protected function handleView(View $view)
    {
        return $this->get('fos_rest.view_handler')->handle($view);
    }

    protected function getResourceFormType()
    {
        return sprintf('%s_%s', $this->getBundlePrefix(), $this->getResourceName());
    }

    protected function getTemplateNamespace()
    {
        return $this->templateNamespace;
    }

    protected function getBundlePrefix()
    {
        return $this->bundlePrefix;
    }

    protected function getResourceName()
    {
        return $this->resourceName;
    }
}
