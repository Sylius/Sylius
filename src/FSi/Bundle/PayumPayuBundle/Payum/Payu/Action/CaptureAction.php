<?php

namespace FSi\Bundle\PayumPayuBundle\Payum\Payu\Action;

use FSi\Bundle\PayumPayuBundle\Payum\Payu\Api;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\Request\PostRedirectUrlInteractiveRequest;
use Payum\Core\Request\SecuredCaptureRequest;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;

class CaptureAction implements ActionInterface, ApiAwareInterface
{
    /**
     * @var Api
     */
    protected $api;

    /**
     * @var Request
     */
    protected $httpRequest;

    /**
     * {@inheritdoc}
     */
    public function setApi($api)
    {
        if (false == $api instanceof Api) {
            throw new UnsupportedApiException('Not supported api type.');
        }

        $this->api = $api;
    }

    /**
     * Define the Symfony Request
     *
     * @param Request $request
     */
    public function setRequest(Request $request = null)
    {
        $this->httpRequest = $request;
    }

    /**
     * @param mixed $request
     *
     * @throws \LogicException
     * @throws \Payum\Core\Request\PostRedirectUrlInteractiveRequest
     * @throws \InvalidArgumentException
     * @throws \Payum\Core\Exception\RequestNotSupportedException
     */
    function execute($request)
    {
        if (false == $this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }

        if (!$this->httpRequest) {
            throw new \LogicException('The action can be run only when http request is set.');
        }

        /* @var $model \Sylius\Bundle\CoreBundle\Model\OrderInterface */
        $model = $request->getModel();

        if ($model->getCurrency() != 'PLN') {
            throw new \InvalidArgumentException(
                sprintf("Currency %s is not supported in PayU payments", $model->getCurrency())
            );
        }

        $details = array(
            'session_id' => $this->httpRequest->getSession()->getId() . time(),
            'amount' => $model->getTotal(),
            'desc' => sprintf(
                'Zamówienie %d przedmiotów na kwotę %01.2f',
                $model->getItems()->count(),
                $model->getTotal() / 100
            ),
            'order_id' => $model->getId(),
            'first_name' => $model->getBillingAddress()->getFirstName(),
            'last_name' => $model->getBillingAddress()->getLastName(),
            'email' => $model->getUser()->getEmail(),
            'client_ip' => $this->httpRequest->getClientIp()
        );

        throw new PostRedirectUrlInteractiveRequest(
            $this->api->getNewPaymentUrl(),
            $this->api->prepareNewPaymentDetails($details)
        );
    }

    /**
     * @param mixed $request
     *
     * @return boolean
     */
    function supports($request)
    {
        return $request instanceof SecuredCaptureRequest &&
            $request->getModel() instanceof OrderInterface;
    }
}