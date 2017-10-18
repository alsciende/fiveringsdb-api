<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;


/**
 * Description of DataImportCommand
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DataImportCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('app:data:import')
            ->setDescription("Import data from JSON files to the database");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $scanningService = $this->getContainer()->get('alsciende_serializer.scanning_service');

        $sources = $scanningService->findSources();

        $serializer = $this->getContainer()->get('alsciende_serializer.serializer');

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $validator = $this->getContainer()->get('validator');

        foreach ($sources as $source) {
            $result = $serializer->importSource($source);
            foreach ($result as $imported) {
                $entity = $imported['entity'];
                $errors = $validator->validate($entity);
                if (count($errors) > 0) {
                    /** @var ConstraintViolationInterface $error */
                    foreach ($errors as $error) {
                        $output->writeln(sprintf('<error>Error while importing %s.</error>', $error->getRoot()));
                    }
                    throw new \Exception((string) $errors);
                }
            }

            $em->flush();
        }
    }

}
