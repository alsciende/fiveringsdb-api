<?php

namespace AppBundle\Command;

use Alsciende\SerializerBundle\Serializer\Deserializer;
use Alsciende\SerializerBundle\Serializer\Serializer;
use Alsciende\SerializerBundle\Service\ScanningService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Description of DataImportCommand
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DataImportCommand extends Command
{
    /** @var ScanningService $scanningService */
    private $scanningService;

    /** @var Serializer $serializer */
    private $serializer;

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var ValidatorInterface $validator */
    private $validator;

    public function __construct ($name = null, ScanningService $scanningService, Serializer $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        parent::__construct($name);
        $this->scanningService = $scanningService;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    protected function configure ()
    {
        $this
            ->setName('app:data:import')
            ->setDescription("Import data from JSON files to the database");
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $sources = $this->scanningService->findSources();

        foreach ($sources as $source) {
            try {
                $result = $this->serializer->importSource($source);
            } catch (\Exception $e) {
                $output->writeln("<error>Error while importing source</error>");
                dump($source);
                throw $e;
            }
            foreach ($result as $imported) {
                $entity = $imported['entity'];
                $errors = $this->validator->validate($entity);
                if (count($errors) > 0) {
                    /** @var ConstraintViolationInterface $error */
                    foreach ($errors as $error) {
                        $output->writeln(sprintf('<error>Error while importing %s.</error>', $error->getRoot()));
                    }
                    throw new \Exception((string) $errors);
                }
            }

            $this->entityManager->flush();
        }
    }
}
