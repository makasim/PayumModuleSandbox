<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Payum\Core\Request\BinaryMaskStatusRequest;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Di\ServiceLocator;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $tokenStorage = $this->getServiceLocator()->get('payum.security.token_storage');
        $storage = $this->getServiceLocator()->get('payum')->getStorageForClass('Application\Model\PaymentDetails', 'paypal');

        $paymentDetails = $storage->createModel();
        $paymentDetails['PAYMENTREQUEST_0_CURRENCYCODE'] = 'EUR';
        $paymentDetails['PAYMENTREQUEST_0_AMT'] = 1.23;
        $storage->updateModel($paymentDetails);

        $doneToken = $tokenStorage->createModel();
        $doneToken->setPaymentName('paypal');
        $doneToken->setDetails($storage->getIdentificator($paymentDetails));
        $doneToken->setTargetUrl($this->url()->fromRoute(
            'done',
            array(),
            array('force_canonical' => true, 'query' => array('payum_token' => $doneToken->getHash()))
        ));
        $tokenStorage->updateModel($doneToken);

        $captureToken = $tokenStorage->createModel();
        $captureToken->setPaymentName('paypal');
        $captureToken->setDetails($storage->getIdentificator($paymentDetails));
        $captureToken->setTargetUrl($this->url()->fromRoute(
            'payum_capture_do',
            array(),
            array('force_canonical' => true, 'query' => array('payum_token' => $captureToken->getHash()))
        ));
        $captureToken->setAfterUrl($doneToken->getTargetUrl());
        $tokenStorage->updateModel($captureToken);

        $paymentDetails['RETURNURL'] = $captureToken->getTargetUrl();
        $paymentDetails['CANCELURL'] = $captureToken->getTargetUrl();
        $storage->updateModel($paymentDetails);
        
        $this->redirect()->toUrl($captureToken->getTargetUrl());
    }

    public function doneAction()
    {
        $token = $this->getServiceLocator()->get('payum.security.http_request_verifier')->verify($this->getRequest());

        $payment = $this->getServiceLocator()->get('payum')->getPayment($token->getPaymentName());

        $payment->execute($status = new BinaryMaskStatusRequest($token));

        var_dump(json_encode(array('status' => $status->getStatus()) + iterator_to_array($status->getModel()), JSON_PRETTY_PRINT));
        die;
    }
}
