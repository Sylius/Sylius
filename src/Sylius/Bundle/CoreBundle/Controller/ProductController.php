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

use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sylius\Bundle\SearchBundle\Query\TaxonQuery;

/**
 * Product controller.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProductController extends ResourceController
{
    /**
     * List products categorized under given taxon.
     *
     * @param Request $request
     * @param string  $permalink
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function indexByTaxonAction(Request $request, $permalink)
    {
        $criteria = $request->get('sylius_filter_form');
        unset($criteria['_token'], $criteria['filter']);

        if ($request->attributes->has('_sylius_entity')) {
            $taxon = $request->attributes->get('_sylius_entity');
        } else {
            $taxon = $this->get('sylius.repository.taxon')
                ->findOneByPermalink($permalink);

            if (!isset($taxon)) {
                throw new NotFoundHttpException('Requested taxon does not exist.');
            }
        }

        $finder = $this->get('sylius_search.finder')
            ->setFacetGroup('categories_set')
            ->find(new TaxonQuery($taxon, $request->query->get('filters')));

        $config = $this->container->getParameter("sylius_search.config");

        $paginator = $finder->getPaginator();

        return $this->renderResults($taxon, $paginator, 'indexByTaxon.html', $request->get('page', 1), $finder->getFacets(), $config['filters']['facets']);
    }

    /**
     * List products categorized under given taxon (fetch by its ID).
     *
     * @param Request $request
     * @param integer $id
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function indexByTaxonIdAction(Request $request, $id)
    {
        $taxon = $this->get('sylius.repository.taxon')->find($id);

        if (!isset($taxon)) {
            throw new NotFoundHttpException('Requested taxon does not exist.');
        }

        $paginator = $this
            ->getRepository()
            ->createByTaxonPaginator($taxon)
        ;

        return $this->renderResults($taxon, $paginator, 'productIndex.html', $request->get('page', 1));
    }

    /**
     * Get product history changes.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function historyAction(Request $request)
    {
        /** @var $product ProductInterface */
        $product = $this->findOr404($request);

        $repository = $this->get('doctrine')->getManager()->getRepository('Gedmo\Loggable\Entity\LogEntry');

        $variants = array();
        foreach ($product->getVariants() as $variant) {
            $variants[] = $repository->getLogEntries($variant);
        }

        $attributes = array();
        foreach ($product->getAttributes() as $attribute) {
            $attributes[] = $repository->getLogEntries($attribute);
        }

        $options = array();
        if (empty($variants)) {
            foreach ($product->getOptions() as $option) {
                $options[] = $repository->getLogEntries($option);
            }
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('history.html'))
            ->setData(array(
                'product' => $product,
                'logs'    => array(
                    'product'    => $repository->getLogEntries($product),
                    'variants'   => $variants,
                    'attributes' => $attributes,
                    'options'    => $options,
                ),
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * Render product filter form.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function filterFormAction(Request $request)
    {
        return $this->render('SyliusWebBundle:Backend/Product:filterForm.html.twig', array(
            'form' => $this->get('form.factory')->createNamed('criteria', 'sylius_product_filter', $request->query->get('criteria'))->createView()
        ));
    }

    // @todo refactor this when PRs about API & search get merged
    public function searchAction(Request $request)
    {
        if (!$request->query->has('criteria')) {
            throw new NotFoundHttpException();
        }

        /** @var $products ProductInterface[] */
        $results  = array();
        $products = $this->get('sylius.repository.product')->createFilterPaginator($request->query->get('criteria'));
        $helper   = $this->get('sylius.templating.helper.currency');
        foreach ($products as $product) {
            $results[] = array(
                'id'        => $product->getMasterVariant()->getId(),
                'name'      => $product->getName(),
                'image'     => $product->getImage()->getPath(),
                'price'     => $helper->convertAndFormatAmount($product->getMasterVariant()->getPrice()),
                'raw_price' => $helper->convertAndFormatAmount($product->getMasterVariant()->getPrice(), null, true),
                'desc'      => $product->getShortDescription(),
            );
        }

        return new JsonResponse($results);
    }

    public function findOr404(Request $request, array $criteria = array())
    {
        if ($request->attributes->has('_sylius_entity')) {
            return $request->attributes->get('_sylius_entity');
        }

        return parent::findOr404($request, $criteria);
    }

    private function renderResults(TaxonInterface $taxon, Pagerfanta $results, $template, $page, $facets = null, $facetTags = null)
    {
        $results->setCurrentPage($page, true, true);
        $results->setMaxPerPage($this->config->getPaginationMaxPerPage());

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate($template))
            ->setData(array(
                'taxon'    => $taxon,
                'products' => $results,
                'facets'   => $facets,
                'facetTags' => $facetTags,
            ))
        ;

        return $this->handleView($view);
    }
}
