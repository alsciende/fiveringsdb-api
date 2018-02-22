<?php

namespace Tests\Functional\Features\Context;

use AppBundle\Entity\Token;
use AppBundle\Entity\User;
use AppBundle\Security\CredentialsCacheService;
use AppBundle\Service\TokenManager;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Doctrine\ORM\EntityManagerInterface;
use JsonSchema\Constraints\Factory;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;
use Nelmio\Alice\Loader\NativeLoader;
use Ramsey\Uuid\Uuid;
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

    /** @var CredentialsCacheService $cache */
    private $cache;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct (CredentialsCacheService $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param KernelInterface $kernel
     */
    public function setKernel (KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given the database is loaded
     */
    public function theDatabaseIsLoaded ()
    {
        $sql = file_get_contents(__DIR__ . '/../../../resources/dump/dump.sql');
        $conn = $this->kernel->getContainer()->get('doctrine.orm.entity_manager')->getConnection();

        if ($conn instanceof \Doctrine\DBAL\Driver\PDOConnection) {
            // PDO Drivers
            try {
                $lines = 0;
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                do {
                    // Required due to "MySQL has gone away!" issue
                    $stmt->fetch();
                    $stmt->closeCursor();
                    $lines++;
                } while ($stmt->nextRowset());
            } catch (\PDOException $e) {
                throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
            }
        } else {
            // Non-PDO Drivers (ie. OCI8 driver)
            $stmt = $conn->prepare($sql);
            $rs = $stmt->execute();
            if ($rs) {
            } else {
                $error = $stmt->errorInfo();
                throw new \RuntimeException($error[2], $error[0]);
            }
            $stmt->closeCursor();
        }
    }

    /**
     * @Given the cache is cleared
     */
    public function theCacheIsCleared()
    {
        $this->cache->clear();
    }

    /**
     * @Given I have a valid cached token for known user :username
     */
    public function iHaveAValidCachedToken ($username)
    {
        $this->token = $username;
        $this->cache->set($this->token, $username);
    }

    /**
     * @Given I have a valid uncached token for user :username
     */
    public function iHaveAValidUncachedToken ($username)
    {
        $this->token = $username;
    }

    /**
     * @Then my token should be cached
     */
    public function myTokenShouldBeCached()
    {
        if($this->cache->has('Bearer ' . $this->token) === false) {
            throw new \Exception('Token is not cached');
        }
    }

    /**
     * @Given I have an invalid token
     */
    public function iHaveAnInvalidToken ()
    {
        $this->token = Uuid::uuid4();
    }

    /**
     * @Given I have a valid uncached token for unknown user :username
     */
    public function iHaveAValidUncachedTokenForUnknownUser($username)
    {
        $this->token = 'user' . $username;
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
     * @Then The response should be successful
     */
    public function theResponseShouldBeSuccessful ()
    {
        if ($this->json->success === false) {
            dump($this->json);
            throw new \Exception('Expected successful response, received unsuccessful response.');
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
        if (empty($this->token)) {
            return [];
        }

        return [
            'HTTP_AUTHORIZATION' => sprintf('Bearer %s', $this->token),
        ];
    }
}