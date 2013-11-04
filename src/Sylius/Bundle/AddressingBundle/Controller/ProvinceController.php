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
use Sylius\Bundle\AddressingBundle\Model\CountryInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Province controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class ProvinceController extends ResourceController
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

        if (!$country = $this->getCountryRepository()->find($countryId)) {
            throw new NotFoundHttpException('Requested country does not exist.');
        }

        if (!$country->hasProvinces()) {
            return new JsonResponse(array('content' => false));
        }

        $form = $this->createProvinceChoiceForm($country);

        $content = $this->renderView($this->getConfiguration()->getTemplate('_provinceChoiceForm.html'), array(
            'form' => $form->createView()
        ));

        return new JsonResponse(array(
            'content' => $content
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        if (null === $countryId = $this->getRequest()->get('countryId')) {
            throw new NotFoundHttpException('No country given');
        }

        $country = $this
            ->getCountryController()
            ->findOr404(array('id' => $countryId))
        ;

        $province = parent::createNew();
        $province->setCountry($country);

        return $province;
    }

    /**
     * Get country repository.
     *
     * @return ObjectRepository
     */
    protected function getCountryRepository()
    {
        return $this->get('sylius.repository.country');
    }

    /**
     * Create province choice form for given country.
     *
     * @param CountryInterface $country
     */
    protected function createProvinceChoiceForm(CountryInterface $country)
    {
        return $this->get('form.factory')->createNamed('sylius_address_province', 'sylius_province_choice', null, array(
            'country'     => $country,
            'label'       => 'sylius.form.address.province',
            'empty_value' => 'sylius.form.province.select'
        ));
    }
}
