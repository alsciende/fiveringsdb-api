<?php

namespace AppBundle\Service;

/**
 * Description of PaypalService
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class PaypalService
{

    private $username;
    private $password;
    private $signature;

    public function __construct ($username, $password, $signature)
    {
        $this->username = $username;
        $this->password = $password;
        $this->signature = $signature;
    }

    private function createRequest ($method, $version, $parameters)
    {
        $exec = "curl https://api-3t.sandbox.paypal.com/nvp -s --insecure";

        $parameters['USER'] = $this->username;
        $parameters['PWD'] = $this->password;
        $parameters['SIGNATURE'] = $this->signature;
        $parameters['METHOD'] = $method;
        $parameters['VERSION'] = $version;

        foreach($parameters as $key => $value) {
            $exec .= " -d $key=$value";
        }

        $output = shell_exec($exec);
        $result = [];
        parse_str($output, $result);

        return $result;
    }

    public function setExpressCheckout ($amount, $currency, $paymentAction)
    {
        return $this->createRequest("SetExpressCheckout", 98, [
                    "PAYMENTREQUEST_0_AMT" => $amount,
                    "PAYMENTREQUEST_0_CURRENCYCODE" => $currency,
                    "PAYMENTREQUEST_0_PAYMENTACTION" => $paymentAction,
                    "cancelUrl" => "http://fiveringsdb.dev/app_dev.php/paypal/cancel",
                    "returnUrl" => "http://fiveringsdb.dev/app_dev.php/paypal/success",
        ]);
    }

    public function getExpressCheckoutRedirectUrl ($token)
    {
        return "https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=$token";
    }

    public function getExpressCheckoutDetails ($token)
    {
        return $this->createRequest("GetExpressCheckoutDetails", 93, [
                    "TOKEN" => $token,
        ]);
    }

    public function doExpressCheckoutPayment ($token, $payerID, $amount, $currency, $paymentAction)
    {
        return $this->createRequest("DoExpressCheckoutPayment", 93, [
                    "TOKEN" => $token,
                    "PAYERID" => $payerID,
                    "PAYMENTREQUEST_0_AMT" => $amount,
                    "PAYMENTREQUEST_0_CURRENCYCODE" => $currency,
                    "PAYMENTREQUEST_0_PAYMENTACTION" => $paymentAction,
        ]);
    }

    public function getTransactionDetails ($transactionId)
    {
        return $this->createRequest("GetTransactionDetails", 78, [
                    "TransactionID" => $transactionId,
        ]);
    }

}
