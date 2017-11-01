<?php

namespace Tests\Functional\Features\Context;

use AppBundle\Entity\Token;
use AppBundle\Entity\User;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Doctrine\ORM\EntityManagerInterface;
use JsonSchema\Constraints\Factory;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;
use Nelmio\Alice\Loader\NativeLoader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Description of FeatureContext
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class FeatureContext implements KernelAwareContext
{
    /** @var KernelInterface */
    private $kernel;

    /** @var string */
    private $token;

    /** @var JsonResponse */
    private $response;

    /** @var string */
    private $content;

    /** @var mixed */
    private $json;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct ()
    {
    }

    /**
     * @param KernelInterface $kernel
     */
    public function setKernel (KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given the database is empty
     */
    public function theDatabaseIsEmpty ()
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->kernel->getContainer()->get('doctrine.orm.entity_manager');

        $metaData = $entityManager->getMetadataFactory()->getAllMetadata();

        $tool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
        $tool->dropSchema($metaData);
        $tool->createSchema($metaData);
    }

    /**
     * @Given the fixtures are loaded
     */
    public function theFixturesAreLoaded ()
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->kernel->getContainer()->get('doctrine.orm.entity_manager');

        $this->loadSourceData($entityManager);
        $this->loadAliceData($entityManager);
    }

    /**
     * @Given I am authenticated as user :username
     */
    public function iAmAuthenticatedAsUser ($username)
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->kernel->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
        if ($user instanceof User) {
            $token = $entityManager->getRepository(Token::class)->findOneBy(['user' => $user]);
            if ($token instanceof Token) {
                $this->token = $token->getId();
                return;
            }
        }

        throw new \Exception('Cannot find a valid token for user ' . $username);
    }

    /**
     * @When I query :url by :method
     */
    public function iQueryBy ($url, $method)
    {
        $client = $this->kernel->getContainer()->get('test.client');
        $client->request($method, $url, [], [], ['CONTENT_TYPE' => 'application/json'] + $this->getAuthorizationHeader());
        $this->response = $client->getResponse();
        $this->content = $this->response->getContent();
        $this->json = json_decode($this->content);
    }

    /**
     * @Then I should get a :code HTTP Response status code
     */
    public function iShouldGetAHttpResponseStatusCode ($code)
    {
        $receivedCode = (string) $this->response->getStatusCode();
        if ($receivedCode !== $code) {
            throw new \Exception(sprintf('Expected a "%s" status code, received "%s".', $code, $receivedCode));
        }
    }

    /**
     * @Then The response json should validate the :schema schema
     */
    public function theResponseJsonShouldValidateTheSchema ($schema)
    {
        $schemaStorage = new SchemaStorage();
        $validator = new Validator(new Factory($schemaStorage));
        $validator->check($this->json, $this->loadJsonSchema($schema));
        if ($validator->isValid() === false) {
            $message = 'JSON does not validate:' . PHP_EOL;
            foreach ($validator->getErrors() as $error) {
                $message .= sprintf("[%s] %s\n", $error['property'], $error['message']);
            }
            var_dump($this->json);
            throw new \Exception($message);
        }
    }

    /**
     * @Then I should have the response equals to expected :filename json
     */
    public function TheResponseShouldEqualsExpected ($filename)
    {
        $expectedJson = $this->loadResourceJson($filename);
        if (json_decode($this->content, true) != $expectedJson) {
            echo "Expected response:\n";
            echo json_encode($expectedJson, JSON_PRETTY_PRINT);
            echo "\n\nActual response:\n";
            echo json_encode($this->json, JSON_PRETTY_PRINT);
            throw new \Exception(sprintf(
                'Actual response does not match expected response. Status code %d.',
                $this->response->getStatusCode()
            ));
        }
    }

    private function loadJsonSchema ($name): \stdClass
    {
        $filepath = sprintf("%s/../tests/resources/schema/%s.json", $this->kernel->getRootDir(), $name);
        if (!file_exists($filepath)) {
            throw new \Exception(sprintf('Schema %s does not exist.', $name));
        }

        return json_decode(file_get_contents($filepath));
    }

    private function loadResourceJson ($name): array
    {
        $filepath = sprintf("%s/../tests/resources/json/%s.json", $this->kernel->getRootDir(), $name);
        if (!file_exists($filepath)) {
            throw new \Exception(sprintf('Json %s does not exist.', $name));
        }

        return json_decode(file_get_contents($filepath), true);
    }

    private function getAuthorizationHeader ()
    {
        return [
            'HTTP_AUTHORIZATION' => sprintf('Bearer %s', $this->token),
        ];
    }

    private function loadSourceData (EntityManagerInterface $entityManager)
    {
        $scanningService = $this->kernel->getContainer()->get('alsciende_serializer.scanning_service');

        $sources = $scanningService->findSources();

        $serializer = $this->kernel->getContainer()->get('alsciende_serializer.serializer');

        $validator = $this->kernel->getContainer()->get('validator');

        foreach ($sources as $source) {
            $result = $serializer->importSource($source);
            foreach ($result as $imported) {
                $entity = $imported['entity'];
                $errors = $validator->validate($entity);
                if (count($errors) > 0) {
                    $errorsString = (string) $errors;
                    throw new \Exception($errorsString);
                }
            }

            $entityManager->flush();
        }
    }

    private function loadAliceData (EntityManagerInterface $entityManager)
    {
        $loader = new NativeLoader();
        $objectSet = $loader->loadFile(
            $this
                ->kernel
                ->getContainer()
                ->getParameter('kernel.project_dir')
            . '/src/AppBundle/DataFixtures/ORM/fixtures.yml'
        );

        foreach ($objectSet->getObjects() as $reference => $object) {
            $entityManager->persist($object);
        }

        $entityManager->flush();
    }

}