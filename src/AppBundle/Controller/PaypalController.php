<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function dump;

/**
 * Description of PaypalController
 *
 * @author Alsciende <alsciende@icloud.com>
 * 
 * @Route("/paypal")
 */
class PaypalController extends Controller
{

    /**
     * @Route("/create", name="paypal_create")
     */
    public function createAction ()
    {
        /* @var $paypalService \AppBundle\Service\PaypalService */
        $paypalService = $this->get('paypal');

        $result = $paypalService->setExpressCheckout(10, 'EUR', 'SALE');

        if(isset($result['TOKEN']) === false) {
            dump($result);
            die;
        }

        $token = $result['TOKEN'];

        return $this->redirect($paypalService->getExpressCheckoutRedirectUrl($token));
    }

    /**
     * @Route("/cancel")
     */
    public function cancelAction ()
    {
        return new Response("Too bad, enjoy your day anyway!");
    }

    /**
     * @Route("/success")
     */
    public function successAction (Request $request)
    {
        $token = $request->query->get('token');
        $payerID = $request->query->get('PayerID');

        /* @var $paypalService \AppBundle\Service\PaypalService */
        $paypalService = $this->get('paypal');

        $result_get = $paypalService->getExpressCheckoutDetails($token);
        dump($result_get);

        $result_do = $paypalService->doExpressCheckoutPayment($token, $payerID, 10, 'EUR', 'SALE');
        dump($result_do);

        return new Response("Thank you dear Sir!");
    }

    /**
     * Example id: 8S49757417107934H
     * 
     * @Route("/transaction/{transactionId}")
     */
    public function transactionAction ($transactionId)
    {
        /* @var $paypalService \AppBundle\Service\PaypalService */
        $paypalService = $this->get('paypal');

        $result = $paypalService->getTransactionDetails($transactionId);
        dump($result);

        return new Response("Done!");
    }

}
