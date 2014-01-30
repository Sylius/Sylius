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
use Sylius\Bundle\ResourceBundle\Event\ResourceEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
     * Get collection (paginated by default) of resources.
     */
    public function indexAction(Request $request)
    {
        $config     = $this->getConfiguration();
        $criteria   = $config->getCriteria();
        $sorting    = $config->getSorting();
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
            ->setTemplateVar($config->getPluralResourceName())
            ->setData($resources)
        ;

        return $this->handleView($view);
    }

    /**
     * Get single resource by its identifier.
     */
    public function showAction(Request $request)
    {
        $config = $this->getConfiguration();

        $view = $this
            ->view()
            ->setTemplate($config->getTemplate('show.html'))
            ->setTemplateVar($config->getResourceName())
            ->setData($this->findOr404($request))
        ;

        return $this->handleView($view);
    }

    /**
     * Create new resource or just display the form.
     */
    public function createAction(Request $request)
    {
        $resource = $this->createNew($request);
        $form = $this->getForm($resource);

        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $event = $this->create($resource);
            if (!$event->isStopped()) {
                $this->setFlash('success', 'create');

                return $this->redirectTo($resource);
            }

            $this->setFlash($event->getMessageType(), $event->getMessage(), $event->getMessageParams());
        }

        $config = $this->getConfiguration();
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
        $resource = $this->findOr404($request);
        $form = $this->getForm($resource);

        if (($request->isMethod('PUT') || $request->isMethod('POST')) && $form->submit($request)->isValid()) {
            $event = $this->update($resource);
            if (!$event->isStopped()) {
                $this->setFlash('success', 'update');

                return $this->redirectTo($resource);
            }

            $this->setFlash($event->getMessageType(), $event->getMessage(), $event->getMessageParams());
        }

        $config = $this->getConfiguration();
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
    public function deleteAction(Request $request)
    {
        $resource = $this->findOr404($request);

        $config = $this->getConfiguration();
        if ($request->request->get('confirmed', false)) {
            $event = $this->delete($resource);

            if ($request->isXmlHttpRequest()) {
                return JsonResponse::create(array('id' => $request->get('id')));
            }

            if ($event->isStopped()) {
                $this->setFlash($event->getMessageType(), $event->getMessage(), $event->getMessageParams());

                return $this->redirectTo($resource);
            }

            $this->setFlash('success', 'delete');

            $config = $this->getConfiguration();

            return $this->redirectToRoute(
                $config->getRedirectRoute('index'),
                $config->getRedirectParameters()
            );
        }

        if ($request->isXmlHttpRequest()) {
            throw new AccessDeniedException();
        }

        $view = $this
            ->view()
            ->setTemplate($config->getTemplate('delete.html'))
        ;

        return $this->handleView($view);
    }

    /**
     * Get a form instance for given resource.
     * If no custom form is specified in route defaults to "_form",
     * then a default name is generated using the template "%bundlePrefix%_%resourceName%".
     *
     * @param null|string $resource
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

    /**
     * Get configuration with the bound request.
     *
     * @return Configuration
     */
    protected function getConfiguration()
    {
        $this->configuration->load($this->getRequest());

        return $this->configuration;
    }

    protected function create($resource)
    {
        return $this->persistAndFlush($resource, 'create');
    }

    protected function update($resource)
    {
        return $this->persistAndFlush($resource, 'update');
    }

    protected function delete($resource)
    {
        return $this->removeAndFlush($resource);
    }

    protected function persistAndFlush($resource, $type)
    {
        $event = $this->dispatchEvent('pre_'.$type, $resource);

        if (!$event->isStopped()) {
            $manager = $this->getManager();
            $manager->persist($resource);

            $this->dispatchEvent($type, $resource);

            $manager->flush();

            $this->dispatchEvent('post_'.$type, $resource);
        }

        return $event;
    }

    protected function removeAndFlush($resource)
    {
        $event = $this->dispatchEvent('pre_delete', $resource);

        if (!$event->isStopped()) {
            $manager = $this->getManager();
            $manager->remove($resource);

            $this->dispatchEvent('delete', $resource);

            $manager->flush();

            $this->dispatchEvent('post_delete', $resource);
        }

        return $event;
    }

    /**
     * @param Request $request
     * @param array   $criteria
     *
     * @return mixed
     *
     * @throws NotFoundHttpException
     */
    protected function findOr404(Request $request, array $criteria = null)
    {
        $config = $this->getConfiguration();

        if (empty($criteria)) {
            $criteria = $config->getCriteria();
        }

        if (empty($criteria)) {
            $criteria = array('id' => $request->get('id'));
        }

        $repository = $this->getRepository();

        if (!$resource = $this->getResourceResolver()->getResource($repository, $config, 'findOneBy', array($criteria))) {
            throw new NotFoundHttpException(sprintf('Requested %s does not exist', $config->getResourceName()));
        }

        return $resource;
    }

    /**
     * Use repository to create a new resource object.
     *
     * @param Request $request
     *
     * @return object
     */
    protected function createNew(Request $request)
    {
        return $this
            ->getRepository()
            ->createNew()
        ;
    }

    protected function renderResponse($templateName, array $parameters = array())
    {
        return $this->render($this->getConfiguration()->getTemplate($templateName), $parameters);
    }

    /**
     * Informs listeners that event data was used
     *
     * @param string       $name
     * @param Event|object $eventOrResource
     */
    protected function dispatchEvent($name, $eventOrResource)
    {
        if (!$eventOrResource instanceof Event) {
            $name = $this->getConfiguration()->getEventName($name);

            $eventOrResource = new ResourceEvent($eventOrResource);
        }

        return $this->get('event_dispatcher')->dispatch($name, $eventOrResource);
    }

    protected function setFlash($type, $event, $params = array())
    {
        return $this
            ->get('session')
            ->getFlashBag()
            ->add($type, $this->generateFlashMessage($event, $params))
        ;
    }

    protected function generateFlashMessage($event, $params = array())
    {
        $config = $this->getConfiguration();

        $message = $config->getFlashMessage($event);
        $translatedMessage = $this->translateFlashMessage($message, $params);

        if ($message !== $translatedMessage) {
            return $translatedMessage;
        }

        return $this->translateFlashMessage('sylius.resource.'.$event, $params);
    }

    protected function translateFlashMessage($message, $params = array())
    {
        $resource = ucfirst(str_replace('_', ' ', $this->getConfiguration()->getResourceName()));

        return $this->get('translator')->trans(
            $message,
            array_merge(array('%resource%' => $resource), $params),
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

    protected function redirectToReferer()
    {
        return $this->handleView($this->redirectView($this->getRequest()->headers->get('referer')));
    }

    protected function redirectToIndex()
    {
        $config = $this->getConfiguration();

        return $this->redirectToRoute($config->getRedirectRoute('index'), $config->getRedirectParameters());
    }

    protected function redirectToRoute($route, array $data = array())
    {
        if ('referer' === $route) {
            return $this->redirectToReferer();
        }

        return $this->handleView($this->redirectView($this->generateUrl($route, $data)));
    }
}
