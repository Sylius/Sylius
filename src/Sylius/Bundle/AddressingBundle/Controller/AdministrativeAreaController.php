<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Controller;

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Addressing\Model\CountryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class AdministrativeAreaController extends ResourceController
{
    /**
     * Renders the province select field.
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function choiceFormAction(Request $request)
    {
        if (!$request->isXmlHttpRequest() || null === $countryId = $request->query->get('countryId')) {
            throw new AccessDeniedException();
        }

        /* @var CountryInterface $country */
        if (!$country = $this->getCountryRepository()->find($countryId)) {
            throw new NotFoundHttpException('Requested country does not exist.');
        }

        if (!$country->hasAdministrativeAreas()) {
            return new JsonResponse(array('content' => false));
        }

        $form = $this->createAdministrativeAreaChoiceForm($country);

        $content = $this->renderView($this->getConfiguration()->getTemplate('_administrativeAreaChoiceForm.html'), array(
            'form' => $form->createView(),
        ));

        return new JsonResponse(array(
            'content' => $content,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        $request = $this->config->getRequest();
        if (null === $countryId = $request->get('countryId')) {
            throw new NotFoundHttpException('No country given.');
        }

        $country = $this
            ->getCountryController()
            ->findOr404($request, array('id' => $countryId))
        ;

        $administrativeArea = parent::createNew();
        $administrativeArea->setCountry($country);

        return $administrativeArea;
    }

    /**
     * @return ResourceController
     */
    protected function getCountryController()
    {
        return $this->get('sylius.controller.country');
    }

    /**
     * @return ObjectRepository
     */
    protected function getCountryRepository()
    {
        return $this->get('sylius.repository.country');
    }

    /**
     * @param CountryInterface $country
     *
     * @return FormInterface
     */
    protected function createAdministrativeAreaChoiceForm(CountryInterface $country)
    {
        return $this->get('form.factory')->createNamed('sylius_address_administrative_area', 'sylius_administrative_area_choice', null, array(
            'country'     => $country,
            'label'       => 'sylius.form.address.administrative_area',
            'empty_value' => 'sylius.form.administrative_area.select',
        ));
    }
}
