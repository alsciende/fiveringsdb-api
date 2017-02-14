<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of DataImportCommand
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DataImportCommand extends ContainerAwareCommand
{

    protected function configure ()
    {
        $this
                ->setName('app:data:import')
                ->setDescription("Import data from JSON files to the database")
        ;
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        /* @var $validator \Symfony\Component\Validator\Validator\RecursiveValidator */
        $validator = $this->getContainer()->get('validator');

        $rootDir = $this->getContainer()->get('kernel')->getRootDir();
        $jsonPath = $this->getContainer()->getParameter('json_data_path');

        $fs = new \Symfony\Component\Filesystem\Filesystem();
        if(!$fs->isAbsolutePath($jsonPath)) {
            $jsonDataPath = $rootDir . '/../' . $jsonPath;
        } else {
            $jsonDataPath = $jsonPath;
        }

        $types = [
            \AppBundle\Entity\Type::class,
            \AppBundle\Entity\Clan::class,
            \AppBundle\Entity\Cycle::class,
            \AppBundle\Entity\Pack::class,
            \AppBundle\Entity\Card::class
        ];

        foreach($types as $className) {

            /* @var $cereal \Alsciende\CerealBundle\Service\Cereal */
            $cereal = new \Alsciende\CerealBundle\Service\Cereal($em, $jsonDataPath, $className);

            $entities = $cereal->import();

            dump($entities);

            foreach($entities as $entity) {
                // @TODO validate
                $errors = $validator->validate($entity);
                if(count($errors) > 0) {
                    $errorsString = (string) $errors;
                    throw new \Exception($errorsString);
                }
                $em->merge($entity);
            }

            $em->flush();
        }
    }

}
