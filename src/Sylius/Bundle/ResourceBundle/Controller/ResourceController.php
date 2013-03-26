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

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Base resource controller for Sylius.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ResourceController extends FOSRestController
{
    /**
     * Controller configuration.
     *
     * @var Configuration
     */
    protected $configuration;
    protected $resolver;

    /**
     * Constructor.
     *
     * @param string $bundlePrefix
     * @param string $resourceName
     * @param string $templateNamespace
     * @param string $templatingEngine
     */
    public function __construct($bundlePrefix, $resourceName, $templateNamespace, $templatingEngine = 'twig')
    {
        $this->configuration = new Configuration($bundlePrefix, $resourceName, $templateNamespace, $templatingEngine);
        $this->configured = false;
    }

    /**
     * Get configuration with the bound request.
     *
     * @return Configuration
     */
    public function getConfiguration()
    {
        $this->configuration->load($this->getRequest());

        return $this->configuration;
    }

    /**
     * Get collection (paginated by default) of resources.
     */
    public function indexAction(Request $request)
    {
        $config = $this->getConfiguration();

        $criteria = $config->getCriteria();
        $sorting = $config->getSorting();

        $pluralName = $config->getPluralResourceName();
        $repository = $this->getRepository();

        if ($config->isPaginated()) {
            $resources = $this
                ->getResourceResolver()
                ->getResource($repository, $config, 'createPaginator', array($criteria, $sorting))
            ;

            $resources
                ->setCurrentPage($request->get('page', 1), true, true)
                ->setMaxPerPage($config->getPaginationMaxPerPage())
            ;
        } else {
            $resources = $this
                ->getResourceResolver()
                ->getResource($repository, $config, 'findBy', array($criteria, $sorting, $config->getLimit()))
            ;
        }

        $view = $this
            ->view()
            ->setTemplate($config->getTemplate('index.html'))
            ->setTemplateVar($pluralName)
            ->setData($resources)
        ;

        return $this->handleView($view);
    }

    /**
     * Get single resource by its identifier.
     */
    public function showAction()
    {
        $config = $this->getConfiguration();

        $view =  $this
            ->view()
            ->setTemplate($config->getTemplate('show.html'))
            ->setTemplateVar($config->getResourceName())
            ->setData($this->findOr404())
        ;

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
            $this->create($resource);
            $this->setFlash('success', 'create');

            return $this->redirectTo($resource);
        }

        if ($config->isApiRequest()) {
            return $this->handleView($this->view($form));
        }

        $view = $this
            ->view()
            ->setTemplate($config->getTemplate('create.html'))
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

        $resource = $this->findOr404();
        $form = $this->getForm($resource);

        if (($request->isMethod('PUT') || $request->isMethod('POST')) && $form->bind($request)->isValid()) {
            $this->update($resource);
            $this->setFlash('success', 'update');

            return $this->redirectTo($resource);
        }

        if ($config->isApiRequest()) {
            return $this->handleView($this->view($form));
        }

        $view = $this
            ->view()
            ->setTemplate($config->getTemplate('update.html'))
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
        $resource = $this->findOr404();

        $this->delete($resource);
        $this->setFlash('success', 'delete');

        return $this->redirectToIndex($resource);
    }

    /**
     * Use repository to create a new resource object.
     *
     * @return object
     */
    public function createNew()
    {
        return $this
            ->getRepository()
            ->createNew()
        ;
    }

    /**
     * Get a form instance for given resource.
     * If no custom form is specified in route defaults as "_form",
     * then a default name is generated using the template "%bundlePrefix%_%resourceName%".
     *
     * @return FormInterface
     */
    public function getForm($resource = null)
    {
        return $this->createForm($this->getConfiguration()->getFormType(), $resource);
    }

    public function redirectTo($resource)
    {
        $config = $this->getConfiguration();
        $parameters = $config->getRedirectParameters();

        if (empty($parameters)) {
            $parameters['id'] = $resource->getId();
        }

        return $this->redirectToRoute(
            $config->getRedirectRoute('show'),
            $parameters
        );
    }

    protected function redirectToReferer()
    {
        return $this->handleView($this->redirectView($this->getRequest()->headers->get('referer')));
    }

    public function redirectToIndex()
    {
        $config = $this->getConfiguration();
        return $this->redirectToRoute($config->getRedirectRoute('index'), $config->getRedirectParameters());
    }

    public function redirectToRoute($route, array $data = array())
    {
        if ('referer' === $route) {
            return $this->redirectToReferer();
        }

        return $this->handleView($this->routeRedirectView($route, $data));
    }

    public function getManager()
    {
        return $this->getService('manager');
    }

    public function create($resource)
    {
        $this->dispatchEvent('pre_create', $resource);
        $this->persistAndFlush($resource);
        $this->dispatchEvent('post_create', $resource);
    }

    public function update($resource)
    {
        $this->dispatchEvent('pre_update', $resource);
        $this->persistAndFlush($resource);
        $this->dispatchEvent('post_update', $resource);
    }

    public function delete($resource)
    {
        $this->dispatchEvent('pre_delete', $resource);
        $this->removeAndFlush($resource);
        $this->dispatchEvent('post_delete', $resource);
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

    public function findOr404(array $criteria = null)
    {
        $config = $this->getConfiguration();

        if (empty($criteria)) {
            $criteria = $config->getCriteria();
        }

        if (empty($criteria)) {
            $criteria = array('id' => $this->getRequest()->get('id'));
        }

        $repository = $this->getRepository();

        if (!$resource = $this->getResourceResolver()->getResource($repository, $config, 'findOneBy', array($criteria))) {
            throw new NotFoundHttpException(sprintf('Requested %s does not exist', $config->getResourceName()));
        }

        return $resource;
    }

    public function renderResponse($templateName, array $parameters = array())
    {
        return $this->render($this->getConfiguration()->getTemplate($templateName), $parameters);
    }

    /**
     * Informs listeners that event data was used
     *
     * @param string       $name
     * @param Event|object $eventOrResource
     */
    public function dispatchEvent($name, $eventOrResource)
    {
        if (!$eventOrResource instanceof Event) {
            $name = $this->getConfiguration()->getEventName($name);

            $eventOrResource = new GenericEvent($eventOrResource);
        }

        $this->get('event_dispatcher')->dispatch($name, $eventOrResource);
    }

    protected function setFlash($type, $event)
    {
        return $this
            ->get('session')
            ->getFlashBag()
            ->add($type, $this->generateFlashMessage($event))
        ;
    }

    protected function generateFlashMessage($event)
    {
        $config = $this->getConfiguration();

        if (null !== $message = $config->getFlashMessage()) {
            return $this->translateFlashMessage($message);
        }

        $message = implode('.', array($config->getBundlePrefix(), $config->getResourceName(), $event));
        $translatedMessage = $this->translateFlashMessage($message);

        if ($message !== $translatedMessage) {
            return $translatedMessage;
        }

        return $this->translateFlashMessage('sylius.resource.'.$event);
    }

    protected function translateFlashMessage($message)
    {
        $resource = ucfirst(str_replace('_', ' ', $this->getConfiguration()->getResourceName()));

        return $this->get('translator')->trans(
            $message,
            array('%resource%' => $resource),
            'flashes'
        );
    }

    protected function getResourceResolver()
    {
        if (null === $this->resolver) {
            $this->resolver = new ResourceResolver();
        }

        return $this->resolver;
    }

    protected function getService($name)
    {
        return $this->get($this->getConfiguration()->getServiceName($name));
    }
}
