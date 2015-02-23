<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Payum\Core\Request\GetHumanStatus;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Di\ServiceLocator;
use Zend\View\Model\JsonModel;

class PaymentController extends AbstractActionController
{
    public function stripeJsAction()
    {
        $storage = $this->getServiceLocator()->get('payum')->getStorage('Application\Model\PaymentDetails');

        $details = $storage->create();
        $details["amount"] = 100;
        $details["currency"] = 'USD';
        $details["description"] = 'a description';
        $storage->update($details);

        $captureToken = $this->getServiceLocator()->get('payum.security.token_factory')->createCaptureToken(
            'stripe_js', $details, 'payment_done'
        );

        $this->redirect()->toUrl($captureToken->getTargetUrl());
    }

    public function paypalEcAction()
    {
        $storage = $this->getServiceLocator()->get('payum')->getStorage('Application\Model\PaymentDetails');

        $details = $storage->create();
        $details['PAYMENTREQUEST_0_CURRENCYCODE'] = 'EUR';
        $details['PAYMENTREQUEST_0_AMT'] = 1.23;
        $storage->update($details);

        $captureToken = $this->getServiceLocator()->get('payum.security.token_factory')->createCaptureToken(
            'paypal_ec', $details, 'payment_done'
        );

        $this->redirect()->toUrl($captureToken->getTargetUrl());
    }

    public function doneAction()
    {
        $token = $this->getServiceLocator()->get('payum.security.http_request_verifier')->verify($this->getRequest());

        $payment = $this->getServiceLocator()->get('payum')->getPayment($token->getPaymentName());

        $payment->execute($status = new GetHumanStatus($token));

        return new JsonModel(array('status' => $status->getValue(), 'details' => iterator_to_array($status->getFirstModel())));
    }
}
