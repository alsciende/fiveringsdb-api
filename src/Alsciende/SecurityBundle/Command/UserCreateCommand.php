<?php

namespace Alsciende\SecurityBundle\Command;

/**
 * Description of UserCreateCommand
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class UserCreateCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{

    protected function configure ()
    {
        $this
                ->setName('alsciende_security:user:create')
                ->setDescription("Create a new user")
        ;
    }

    protected function execute (\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $helper = $this->getHelper('question');

        $question = new \Symfony\Component\Console\Question\Question("Username: ");
        $username = $helper->ask($input, $output, $question);

        $user = new \AppBundle\Entity\User($username, md5(uniqid()));

        $em->persist($user);
        $em->flush();

        $output->write("Created user <info>".$user->getId()."</info>\n");
    }

}
