<?php

namespace AppBundle\Command;

use AppBundle\Service\PaypalService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of PaypalTransaction
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class PaypalTransactionCommand extends Command
{
    /** @var PaypalService $paypalService */
    private $paypalService;

    public function __construct ($name = null, PaypalService $paypalService)
    {
        parent::__construct($name);
        $this->paypalService = $paypalService;
    }

    protected function configure ()
    {
        $this
            ->setName('app:paypal:transaction')
            ->setDescription("Fetch and display details about a PayPal Transaction")
            ->addArgument('transaction_id', InputArgument::REQUIRED, "ID of the Paypal transaction");
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $transactionId = $input->getArgument('transaction_id');

        $result = $this->paypalService->getTransactionDetails($transactionId);
//        dump($result);
    }
}
