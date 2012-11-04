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
abstract class ResourceController extends Controller implements ResourceControllerInterface
{
    /**
     * Get one resource.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getAction(Request $request)
    {
        $identifier = $this->getIdentifierValue($request);
        $resource = $this->findResourceOr404($identifier);

        $view = View::create()
            ->setTemplate($this->getFullTemplateName('show'))
            ->setTemplateVar($this->getResourceName())
            ->setData($resource)
        ;

        return $this->handleView($view);
    }

    /**
     * Get all paginated resourcees.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getCollectionAction(Request $request)
    {
        $paginator = $this->getManager()->createPaginator();
        $paginator->setCurrentPage($request->query->get('page', 1), true, true);

        $resources = $paginator->getCurrentPageResults();

        $data = $this->isHtmlRequest() ? array(Pluralization::pluralize($this->getResourceName()) => $resources, 'paginator' => $paginator) : $resourcees;

        $view = View::create()
            ->setTemplate($this->getFullTemplateName('list'))
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

        if ($request->isMethod('POST') && $form->bind()->isValid()) {
            $this->getManipulator()->create($resource);
            $this->setFlash('success', sprintf("%s has been created", ucfirst($this->getResourceName())));

            return $this->redirectToResource($resource);
        }

        $htmlView = View::create()
            ->setTemplate($this->getFullTemplateName('create.html'))
            ->setData(array(
                $this->getResourceName() => $resource,
                'form' => $form->createView()
            ))
        ;

        $view = $this->isHtmlRequest() ? $htmlView : View::create($form);

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

        $htmlView = View::create()
            ->setTemplate($this->getFullTemplateName('update.html'))
            ->setData(array(
                $this->getResourceName() => $resource,
                'form' => $form->createView()
            ))
        ;

        $view = $this->isHtmlRequest() ? $htmlView : View::create($form);

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
     * @return ManagerInterface
     */
    protected function getManager()
    {
        return $this->get($this->getServiceName('manager'));
    }

    protected function getServiceName($name)
    {
        return sprintf('%s.%s.%s', $this->getBundlePrefix(), $name, $this->getResourceName());
    }

    /**
     * Tries to find resource with given id.
     * Throws special 404 exception when unsuccessful.
     *
     * @param mixed $id The resource identifier
     *
     * @return ResourceInterface
     *
     * @throws NotFoundHttpException
     */
    protected function findResourceOr404($identifier)
    {
        $criteria = array($this->getIdentifierName() => $this->getIdentifierValue());

        if (!$resource = $this->getManager()->findOneBy($criteria)) {
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
        if (!$identifier = $this->getRequest()->get($this->getIdentifierName())) {
            throw new NotFoundHttpException('No resource identifier supplied');
        }

        return $identifier;
    }

    protected function renderResponse($templateName, array $parameters = array())
    {
        return $this->render($this->getFullTemplateName($templateName), $parameters);
    }

    /**
     * Get full template name.
     *
     * @param string
     *
     * @return string
     */
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

    /**
     * Get engine.
     *
     * @return string
     */
    protected function getEngine()
    {
        return $this->container->getParameter(sprintf('%s.engine', $this->getBundlePrefix()));
    }

    /**
     * Check if request accepts html format.
     *
     * @return Boolean
     */
    protected function isHtmlRequest()
    {
        return 'html' === $this->getRequest()->getRequestFormat();
    }

    /**
     * Convert view to a response object.
     *
     * @param View $view
     *
     * @return Response
     */
    protected function handleView(View $view)
    {
        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * Get name of the form type to use.
     *
     * @return string
     */
    protected function getResourceFormType()
    {
        return sprintf('%s_%s', $this->getBundlePrefix(), $this->getResourceName());
    }

    /**
     * Get templates namespace.
     *
     * @return string
     */
    abstract protected function getTemplateNamespace();
    abstract protected function getBundlePrefix();
}
