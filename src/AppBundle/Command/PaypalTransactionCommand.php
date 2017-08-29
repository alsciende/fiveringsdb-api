<?php

namespace AppBundle\Command;

/**
 * Description of PaypalTransaction
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class PaypalTransactionCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{

    protected function configure ()
    {
        $this
            ->setName('app:paypal:transaction')
            ->setDescription("Fetch and display details about a PayPal Transaction")
            ->addArgument('transaction_id', \Symfony\Component\Console\Input\InputArgument::REQUIRED, "ID of the Paypal transaction")
        ;
    }

    protected function execute (\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $transactionId = $input->getArgument('transaction_id');

        /* @var $service \AppBundle\Service\PaypalService */
        $service = $this->getContainer()->get('paypal');

        $result = $service->getTransactionDetails($transactionId);

        dump($result);
    }

}
