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
use Sylius\Bundle\ResourceBundle\Configuration\ResourceConfiguration;
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
    protected $configuration;

    public function __construct($bundlePrefix, $resourceName, $templateNamespace)
    {
        $this->configuration = new ResourceConfiguration($bundlePrefix, $resourceName, $templateNamespace);
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

        $resource = $this->create();
        $form = $this->getForm($resource);

        if ($request->isMethod('POST') && $form->bind($request)->isValid()) {
            $this->getManager()->persist($resource);

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
            $this->getManager()->persist($resource);

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
     * Delete resource.
     */
    public function deleteAction()
    {
        $criteria = $this
            ->getConfiguration()
            ->getIdentifierCriteria()
        ;

        $resource = $this->findOr404($criteria);
        $this->getManager()->remove($resource);

        return $this->redirectToCollection();
    }

    public function getConfiguration(array $parameters = null)
    {
        $source = $parameters ?: $this->getRequest();

        $this->configuration->load($source);

        return $this->configuration;
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
        $config = $this->getConfiguration();

        if (null !== $type = $config->getFormType()) {
            return $type;
        }

        return sprintf('%s_%s', $config->getBundlePrefix(), $config->getResourceName());
    }

    protected function redirectTo(ResourceInterface $resource)
    {
        $route = $this->getRedirectRoute('show');
        
        return $this->handleView(RouteRedirectView::create($route, array('id' => $resource->getId())));
    }

    protected function redirectToCollection()
    {        
        $route = $this->getRedirectRoute('list');

        return $this->handleView(RouteRedirectView::create($route));
    }

    protected function getRedirectRoute($name)
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
        return $this->get($this->getConfiguration()->getServiceName($name));
    }

    protected function findOr404(array $criteria)
    {
        if (!$resource = $this->getRepository()->findOneBy($criteria)) {
            throw new NotFoundHttpException(sprintf('Requested %s does not exist', $this->getConfiguration()->getResourceName()));
        }

        return $resource;
    }

    protected function renderResponse($templateName, array $parameters = array())
    {
        return $this->render($this->getFullTemplateName($templateName), $parameters);
    }

    protected function getFullTemplateName($name)
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

    protected function getEngine()
    {
        return $this->container->getParameter(sprintf('%s.engine', $this->getConfiguration()->getBundlePrefix()));
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
            ->add($type, $message)
        ;
    }
}
