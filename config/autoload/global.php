<?php
use Payum\Core\Storage\FilesystemStorage;
use Payum\Paypal\ExpressCheckout\Nvp\Api;
use Payum\Paypal\ExpressCheckout\Nvp\PaymentFactory as PaypalEcPaymentFactory;
use Payum\Stripe\Keys;
use Payum\Stripe\PaymentFactory as StripePaymentFactory;

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

$paypalEcPayment = PaypalEcPaymentFactory::create(new Api(array(
    'username' => 'testrj_1312968849_biz_api1.remixjobs.com',
    'password' => '1312968888',
    'signature' => 'Azgw.f7NYjBAlDQEpbI1D06D4ACAAXfoVSV7k4JUuGAPRHzhDbQR2r90',
    'sandbox' => true
)));

$stripeJsPayment = StripePaymentFactory::createJs(
    new Keys('pk_test_CHXb4QMWMv9dCqBZaXpUULyl', 'sk_test_LwdYAFTlciJL5WzULQjzgC1p')
);

return array(
    'payum' => array(
        'token_storage' => new FilesystemStorage(
            __DIR__.'/../../data',
            'Application\Model\PaymentSecurityToken',
            'hash'
        ),
        'payments' => array(
            'paypal_es' => $paypalEcPayment,
            'stripe_js' => $stripeJsPayment,
        ),
        'storages' => array(
            'Application\Model\PaymentDetails' => new FilesystemStorage(
                __DIR__.'/../../data',
                'Application\Model\PaymentDetails'
            )
        )
    ),
);
