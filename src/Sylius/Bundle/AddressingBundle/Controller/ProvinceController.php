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

        /* @var CountryInterface $country */
        if (!$country = $this->getCountryRepository()->find($countryId)) {
            throw new NotFoundHttpException('Requested country does not exist.');
        }

        if (!$country->hasProvinces()) {
            return new JsonResponse(['content' => false]);
        }

        $form = $this->createProvinceChoiceForm($country);

        $content = $this->renderView($this->getConfiguration()->getTemplate('_provinceChoiceForm.html'), [
            'form' => $form->createView(),
        ]);

        return new JsonResponse([
            'content' => $content,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        $request = $this->config->getRequest();
        if (null === $countryId = $request->get('countryId')) {
            throw new NotFoundHttpException('No country given');
        }

        $country = $this
            ->getCountryController()
            ->findOr404($request, ['id' => $countryId])
        ;

        $province = parent::createNew();
        $province->setCountry($country);

        return $province;
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
    protected function createProvinceChoiceForm(CountryInterface $country)
    {
        return $this->get('form.factory')->createNamed('sylius_address_province', 'sylius_province_choice', null, [
            'country' => $country,
            'label' => 'sylius.form.address.province',
            'empty_value' => 'sylius.form.province.select',
        ]);
    }
}
