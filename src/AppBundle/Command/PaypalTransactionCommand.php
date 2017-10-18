<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of PaypalTransaction
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class PaypalTransactionCommand extends ContainerAwareCommand
{

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

        $service = $this->getContainer()->get('paypal');

        $result = $service->getTransactionDetails($transactionId);

//        dump($result);
    }

}
