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
use FOS\RestBundle\View\RedirectView;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Base resource controller for Sylius.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class ResourceController extends Controller
{
    protected $configuration;

    public function __construct($bundlePrefix, $resourceName, $templateNamespace)
    {
        $this->configuration = new Configuration($bundlePrefix, $resourceName, $templateNamespace);
    }

    /**
     * Get single resource by its identifier.
     */
    public function getAction(array $parameters = null)
    {
        $config = $this->getConfiguration($parameters);
        $resource = $this->findOr404($config->getIdentifierCriteria());

        $view = View::create()
            ->setTemplate($this->getFullTemplateName('show.html'))
            ->setTemplateVar($config->getResourceName())
            ->setData($resource)
        ;

        return $this->handleView($view);
    }

    /**
     * Get collection (paginated by default) of resources.
     */
    public function getCollectionAction(Request $request, array $parameters = null)
    {
        $config = $this->getConfiguration($parameters);

        $pluralName = Pluralization::pluralize($config->getResourceName());
        $criteria = $config->getCriteria();
        $sorting = $config->getSorting();

        $view = View::create()
            ->setTemplate($this->getFullTemplateName('list.html'))
        ;

        if ($config->isCollectionPaginated()) {
            $paginator = $this
                ->getRepository()
                ->createPaginator($criteria, $sorting)
            ;

            $paginator->setCurrentPage($request->get('page', 1), true, true);
            $paginator->setMaxPerPage($config->getPaginationMaxPerPage());

            $resources = $paginator->getCurrentPageResults();

            $data = $config->isHtmlRequest() ? array(
                $pluralName => $resources,
                'paginator' => $paginator
            ) : $resources;
        } else {
            $view->setTemplateVar($pluralName);

            $data = $this
                ->getRepository()
                ->findBy($criteria, $sorting, $config->getCollectionLimit())
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
        $config = $this->getConfiguration();

        $resource = $this->createNew();
        $form = $this->getForm($resource);

        if ($request->isMethod('POST') && $form->bind($request)->isValid()) {
            $this->dispatchEvent('pre_create', $resource);
            $this->persistAndFlush($resource);
            $this->dispatchEvent('post_create', $resource);

            $this->setFlash('success', '%resource% has been successfully created.');

            return $this->redirectTo($resource);
        }

        if (!$config->isHtmlRequest()) {
            return $this->handleView(View::create($form));
        }

        $view = View::create()
            ->setTemplate($this->getFullTemplateName('create.html'))
            ->setData(array(
                $config->getResourceName() => $resource,
                'form'                     => $form->createView()
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * Display the form for editing or update the resource.
     */
    public function updateAction(Request $request)
    {
        $config = $this->getConfiguration();

        $resource = $this->findOr404($config->getIdentifierCriteria());
        $form = $this->getForm($resource);

        if ($request->isMethod('POST') && $form->bind($request)->isValid()) {
            $this->dispatchEvent('pre_update', $resource);
            $this->persistAndFlush($resource);
            $this->dispatchEvent('post_update', $resource);

            $this->setFlash('success', '%resource% has been updated.');

            return $this->redirectTo($resource);
        }

        if (!$config->isHtmlRequest()) {
            return $this->handleView(View::create($form));
        }

        $view = View::create()
            ->setTemplate($this->getFullTemplateName('update.html'))
            ->setData(array(
                $config->getResourceName() => $resource,
                'form'                     => $form->createView()
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * Delete resource.
     */
    public function deleteAction()
    {
        $criteria = $this
            ->getConfiguration()
            ->getIdentifierCriteria()
        ;

        $resource = $this->findOr404($criteria);
        $this->removeAndFlush($resource);
        $this->setFlash('success', '%resource% has been deleted.');

        return $this->redirectToCollection();
    }

    public function getConfiguration(array $parameters = null)
    {
        $source = $parameters ?: $this->getRequest();

        $this->configuration->load($source);

        return $this->configuration;
    }

    public function createNew()
    {
        return $this
            ->getRepository()
            ->createNew()
        ;
    }

    public function getForm($resource = null)
    {
        return $this->createForm($this->getFormType(), $resource);
    }

    public function getFormType()
    {
        $config = $this->getConfiguration();

        if (null !== $type = $config->getFormType()) {
            return $type;
        }

        return sprintf('%s_%s', $config->getBundlePrefix(), $config->getResourceName());
    }

    public function redirectTo($resource)
    {
        return $this->redirectToRoute(
            $this->getRedirectRoute('show'),
            array('id' => $resource->getId())
        );
    }

    public function redirectToCollection()
    {
        return $this->redirectToRoute($this->getRedirectRoute('list'));
    }

    public function redirectToRoute($route, array $data = array())
    {
        if ('referer' === $route) {
            return $this->handleView(RedirectView::create($this->getRequest()->headers->get('referer')));
        }

        return $this->handleView(RouteRedirectView::create($route, $data));
    }

    public function getRedirectRoute($name)
    {
        $config = $this->getConfiguration();

        if (null !== $route = $config->getRedirect()) {
            return $route;
        }

        return sprintf('%s_%s_%s',
            $config->getBundlePrefix(),
            $config->getResourceName(),
            $name
        );
    }

    public function getManager()
    {
        return $this->getService('manager');
    }

    public function persistAndFlush($resource)
    {
        $manager = $this->getManager();

        $manager->persist($resource);
        $manager->flush();
    }

    public function removeAndFlush($resource)
    {
        $manager = $this->getManager();

        $manager->remove($resource);
        $manager->flush();
    }

    public function getRepository()
    {
        return $this->getService('repository');
    }

    public function findOr404(array $criteria)
    {
        if (!$resource = $this->getRepository()->findOneBy($criteria)) {
            throw new NotFoundHttpException(sprintf('Requested %s does not exist', $this->getConfiguration()->getResourceName()));
        }

        return $resource;
    }

    public function renderResponse($templateName, array $parameters = array())
    {
        return $this->render($this->getFullTemplateName($templateName), $parameters);
    }

    public function getFullTemplateName($name)
    {
        $config = $this->getConfiguration();

        if (null !== $template = $config->getTemplate()) {
            return $template;
        }

        return sprintf('%s:%s.%s',
            $config->getTemplateNamespace(),
            $name,
            $this->getEngine()
        );
    }

    public function dispatchEvent($name, $resource)
    {
        $config = $this->getConfiguration();

        $this->get('event_dispatcher')->dispatch(sprintf('%s.%s.%s', $config->getBundlePrefix(), $config->getResourceName(), $name), new GenericEvent($resource));
    }

    public function getEngine()
    {
        return $this->container->getParameter(sprintf('%s.engine', $this->getConfiguration()->getBundlePrefix()));
    }

    public function handleView(View $view)
    {
        return $this->get('fos_rest.view_handler')->handle($view);
    }

    protected function setFlash($type, $message)
    {
        $config = $this->getConfiguration();

        if (null !== $customMessage = $config->getFlashMessage()) {
            $message = $customMessage;
        }

        return $this
            ->get('session')
            ->getFlashBag()
            ->add(
                $type,
                $this->get('translator')->trans(
                    $message,
                    array('%resource%' => ucfirst($config->getResourceName())),
                    'flashes'
                )
            )
        ;
    }

    protected function getService($name)
    {
        return $this->get($this->getConfiguration()->getServiceName($name));
    }
}
