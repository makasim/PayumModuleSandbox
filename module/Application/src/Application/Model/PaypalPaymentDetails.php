<?php
namespace Application\Model;

use Payum\Paypal\ExpressCheckout\Nvp\Model\PaymentDetails;

class PaypalPaymentDetails extends  PaymentDetails
{
    protected $id;

    public function getId()
    {
        return $this->id;
    }
}