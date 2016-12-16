<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShopBundle\Controller;

use Sylius\Bundle\CoreBundle\EmailManager\ContactEmailManager;
use Sylius\Bundle\CoreBundle\Form\Type\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class ContactController extends Controller
{
    /**
     * @var ContactEmailManager
     */
    private $contactEmailManager;

    /**
     * @param ContactEmailManager $contactEmailManager
     */
    public function __construct(ContactEmailManager $contactEmailManager)
    {
        $this->contactEmailManager = $contactEmailManager;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function requestAction(Request $request)
    {
        $formType = $this->getSyliusAttribute($request, 'form', ContactType::class);
        $form = $this->createForm($formType);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $data = $form->getData();

            if (!$this->contactEmailManager->sendContactRequest($data)) {
                $flashMessage = $this->getSyliusAttribute($request, 'error_flash', 'sylius.contact.request_error');
                $this->addFlash('error', $flashMessage);

                return $this->redirect($request->headers->get('referer'));
            }

            $flashMessage = $this->getSyliusAttribute($request, 'success_flash', 'sylius.contact.request_success');
            $this->addFlash('success', $flashMessage);

            $redirectRoute = $this->getSyliusAttribute($request, 'redirect', 'referer');

            return $this->redirectToRoute($redirectRoute);
        }

        $template = $this->getSyliusAttribute($request, 'template');

        return $this->render(
            $template,
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param Request $request
     * @param string $attribute
     * @param mixed $default
     *
     * @return mixed
     */
    private function getSyliusAttribute(Request $request, $attribute, $default = null)
    {
        $attributes = $request->attributes->get('_sylius');

        return isset($attributes[$attribute]) ? $attributes[$attribute] : $default;
    }
}
