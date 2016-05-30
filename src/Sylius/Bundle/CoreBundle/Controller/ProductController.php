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

use FOS\RestBundle\View\View;
use Gedmo\Loggable\Entity\LogEntry;
use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\SearchBundle\Query\TaxonQuery;
use Sylius\Component\Archetype\Model\ArchetypeInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProductController extends ResourceController
{
    /**
     * @param Request $request
     * @param string $permalink
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function indexByTaxonAction(Request $request, $permalink)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $criteria = $request->get('sylius_filter_form');
        unset($criteria['_token'], $criteria['filter']);

        if ($request->attributes->has('_sylius_entity')) {
            $taxon = $request->attributes->get('_sylius_entity');
        } else {
            $taxon = $this->container->get('sylius.repository.taxon')
                ->findOneByPermalink($permalink);

            if (!isset($taxon)) {
                throw new NotFoundHttpException('Requested taxon does not exist.');
            }
        }

        /*
         * when using elastic search if you want to setup multiple indexes and control
         * them separately you can do so by adding the index service with a setter
         *
         * ->setTargetIndex($this->get('fos_elastica.index.my_own_index'))
         *
         * where my_own_index is the index name used in the configuration
         * fos_elastica:
         *      indexes:
         *          my_own_index:
         */
        $finder = $this->container->get('sylius_search.finder')
            ->setFacetGroup('categories_set')
            ->find(new TaxonQuery($taxon, $request->query->get('filters', [])));

        $config = $this->container->getParameter('sylius_search.config');

        $paginator = $finder->getPaginator();

        return $this->renderResults(
            $configuration,
            $taxon,
            $paginator,
            'indexByTaxon.html',
            $request->get('page', 1),
            $finder->getFacets(),
            $config['filters']['facets'],
            $finder->getFilters(),
            $this->container->get('sylius_search.request_handler')->getQuery(),
            $this->container->get('sylius_search.request_handler')->getSearchParam(),
            $this->container->getParameter('sylius_search.request.method')
        );
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function indexByTaxonIdAction(Request $request, $id)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $taxon = $this->container->get('sylius.repository.taxon')->find($id);

        if (!isset($taxon)) {
            throw new NotFoundHttpException('Requested taxon does not exist.');
        }

        $paginator = $this->repository->createByTaxonPaginator($taxon);

        return $this->renderResults($configuration, $taxon, $paginator, 'productIndex.html', $request->get('page', 1));
    }

    /**
     * @param Request $request
     * @param string $code
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function indexByArchetypeCodeAction(Request $request, $code)
    {
        $archetype = $this->get('sylius.repository.product_archetype')->findOneByCode($code);

        if (null === $archetype) {
            throw new NotFoundHttpException('Requested archetype does not exist.');
        }

        $paginator = $this
            ->get('sylius.repository.product')
            ->createByProductArchetypePaginator($archetype)
        ;

        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        return $this->renderArchetypeResults($configuration, $archetype, $paginator, 'productIndex.html', $request->query->get('page', 1));
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function indexByArchetypeIdAction(Request $request, $id)
    {
        $archetype = $this->get('sylius.repository.product_archetype')->find($id);

        if (null === $archetype) {
            throw new NotFoundHttpException('Requested archetype does not exist.');
        }

        $paginator = $this
            ->get('sylius.repository.product')
            ->createByProductArchetypePaginator($archetype)
        ;

        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        return $this->renderArchetypeResults($configuration, $archetype, $paginator, 'productIndex.html', $request->query->get('page', 1));
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function detailsAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $channel = $this->container->get('sylius.context.channel')->getChannel();
        $product = $this->findOr404($configuration);

        if (!$product->getChannels()->contains($channel)) {
            throw new NotFoundHttpException(sprintf(
                'Requested %s does not exist for channel: %s.',
                $this->metadata->getName(),
                $channel->getName()
            ));
        }

        $view = View::create()
            ->setTemplate($configuration->getTemplate('show.html'))
            ->setTemplateVar($this->metadata->getName())
            ->setData($product)
        ;

        return $this->viewHandler->handle($configuration, $view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function historyAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        /** @var $product ProductInterface */
        $product = $this->findOr404($configuration);

        $repository = $this->get('doctrine')->getManager()->getRepository(LogEntry::class);

        $variants = [];
        foreach ($product->getVariants() as $variant) {
            $variants[] = $repository->getLogEntries($variant);
        }

        $attributes = [];
        foreach ($product->getAttributes() as $attribute) {
            $attributes[] = $repository->getLogEntries($attribute);
        }

        $options = [];
        if (empty($variants)) {
            foreach ($product->getOptions() as $option) {
                $options[] = $repository->getLogEntries($option);
            }
        }

        $view = View::create()
            ->setTemplate($configuration->getTemplate('history.html'))
            ->setData([
                'product' => $product,
                'logs' => [
                    'product' => $repository->getLogEntries($product),
                    'variants' => $variants,
                    'attributes' => $attributes,
                    'options' => $options,
                ],
            ])
        ;

        return $this->viewHandler->handle($configuration, $view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function filterFormAction(Request $request)
    {
        return $this->container->get('templating')->renderResponse('SyliusWebBundle:Backend/Product:filterForm.html.twig', [
            'form' => $this->container->get('form.factory')->createNamed('criteria', 'sylius_product_filter', $request->query->get('criteria'))->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function searchAction(Request $request)
    {
        if (!$request->query->has('criteria')) {
            throw new NotFoundHttpException();
        }

        /* @var $products ProductInterface[] */
        $results = [];
        $products = $this->container->get('sylius.repository.product')->createFilterPaginator($request->query->get('criteria'));
        $helper = $this->container->get('sylius.templating.helper.currency');
        foreach ($products as $product) {
            $results[] = [
                'id' => $product->getFirstVariant()->getId(),
                'name' => $product->getName(),
                'image' => $product->getFirstVariant()->getImage() ? $product->getFirstVariant()->getImage()->getPath() : null,
                'price' => $helper->convertAndFormatAmount($product->getPrice()),
                'original_price' => $helper->convertAndFormatAmount($product->getFirstVariant()->getOriginalPrice()),
                'raw_price' => $helper->convertAndFormatAmount($product->getPrice(), null, true),
                'desc' => $product->getShortDescription(),
            ];
        }

        return new JsonResponse($results);
    }

    /**
     * @param RequestConfiguration $configuration
     *
     * @return ProductInterface|null
     */
    public function findOr404(RequestConfiguration $configuration)
    {
        $request = $configuration->getRequest();

        if ($request->attributes->has('_sylius_entity')) {
            return $request->attributes->get('_sylius_entity');
        }

        return parent::findOr404($configuration);
    }

    /**
     * @param RequestConfiguration $configuration
     * @param TaxonInterface $taxon
     * @param Pagerfanta $results
     * @param mixed $template
     * @param mixed $page
     * @param mixed|null $facets
     * @param mixed|null $facetTags
     * @param mixed|null $filters
     * @param mixed|null $searchTerm
     * @param mixed|null $searchParam
     * @param mixed|null $requestMethod
     *
     * @return mixed
     */
    private function renderResults(
        RequestConfiguration $configuration,
        TaxonInterface $taxon,
        Pagerfanta $results,
        $template, $page,
        $facets = null,
        $facetTags = null,
        $filters = null,
        $searchTerm = null,
        $searchParam = null,
        $requestMethod = null
    ) {
        $results->setCurrentPage($page, true, true);
        $results->setMaxPerPage($configuration->getPaginationMaxPerPage());

        $view = View::create()
            ->setTemplate($configuration->getTemplate($template))
            ->setData([
                'taxon' => $taxon,
                'products' => $results,
                'facets' => $facets,
                'facetTags' => $facetTags,
                'filters' => $filters,
                'searchTerm' => $searchTerm,
                'searchParam' => $searchParam,
                'requestMethod' => $requestMethod,
            ])
        ;

        return $this->viewHandler->handle($configuration, $view);
    }

    /**
     * @param RequestConfiguration $configuration
     * @param ArchetypeInterface $archetype
     * @param Pagerfanta $results
     * @param string $template
     * @param int $page
     *
     * @return Response
     */
    private function renderArchetypeResults(
        RequestConfiguration $configuration,
        ArchetypeInterface $archetype,
        Pagerfanta $results,
        $template,
        $page
    ) {
        $results->setCurrentPage($page, true, true);
        $results->setMaxPerPage($configuration->getPaginationMaxPerPage());

        $view = View::create()
            ->setTemplate($configuration->getTemplate($template))
            ->setData([
                'archetype' => $archetype,
                'products' => $results,
            ])
        ;

        return $this->viewHandler->handle($configuration, $view);
    }
}
