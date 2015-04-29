<?php
use Payum\Core\Storage\FilesystemStorage;
use Payum\Paypal\ExpressCheckout\Nvp\PaypalExpressCheckoutGatewayFactory;
use Payum\Stripe\StripeJsGatewayFactory;

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

$paypalFactory = new PaypalExpressCheckoutGatewayFactory();
$stripeFactory = new StripeJsGatewayFactory();

return array(
    'payum' => array(
        'token_storage' => new FilesystemStorage(
            __DIR__.'/../../data',
            'Application\Model\PaymentSecurityToken',
            'hash'
        ),
        'gateways' => array(
            'paypal_ec' => $paypalFactory->create(array(
                'username' => 'testrj_1312968849_biz_api1.remixjobs.com',
                'password' => '1312968888',
                'signature' => 'Azgw.f7NYjBAlDQEpbI1D06D4ACAAXfoVSV7k4JUuGAPRHzhDbQR2r90',
                'sandbox' => true
            )),
            'stripe_js' => $stripeFactory->create(array(
                'publishable_key' => 'pk_test_CHXb4QMWMv9dCqBZaXpUULyl',
                'secret_key' => 'sk_test_LwdYAFTlciJL5WzULQjzgC1p',
            )),
        ),
        'storages' => array(
            'Application\Model\PaymentDetails' => new FilesystemStorage(
                __DIR__.'/../../data',
                'Application\Model\PaymentDetails'
            )
        )
    ),
);
