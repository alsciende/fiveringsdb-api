<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Alsciende\SerializerBundle\Serializer\Serializer;
use Alsciende\SerializerBundle\Service\ScanningService;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Description of LoadSourceData
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class LoadSourceData extends Fixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /** @var ScanningService $scanningService */
    private $scanningService;

    /** @var Serializer $serializer */
    private $serializer;

    /** @var ValidatorInterface $validator */
    private $validator;

    public function __construct (
        $name = null,
        ScanningService $scanningService,
        Serializer $serializer,
        ValidatorInterface $validator
    ) {
        $this->scanningService = $scanningService;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function load (ObjectManager $manager)
    {
        $sources = $this->scanningService->findSources();

        foreach ($sources as $source) {
            $result = $this->serializer->importSource($source);
            foreach ($result as $imported) {
                $entity = $imported['entity'];
                $errors = $this->validator->validate($entity);
                if (count($errors) > 0) {
                    $errorsString = (string) $errors;
                    throw new \Exception($errorsString);
                }
            }

            $manager->flush();
        }
    }

    public function getOrder ()
    {
        return 1;
    }
}
