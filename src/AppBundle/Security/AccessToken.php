<?php
/**
 * Created by PhpStorm.
 * User: cedric
 * Date: 20/10/17
 * Time: 15:39
 */

namespace AppBundle\Security;


/**
 */
class AccessToken
{
    private $accessToken;

    private $expiresIn;

    private $tokenType;

    private $scope;

    private $refreshToken;

    private $createdAt;

    private $expiresAt;

    static function createFromJson(string $json): self
    {
        $response = json_decode($json, true);

        if (json_last_error()) {
            throw new \InvalidArgumentException('Cannot decode json token.');
        }

        $accessToken = new self();

        $accessToken->accessToken = $response['access_token'];
        $accessToken->expiresIn = $response['expires_in'];
        $accessToken->tokenType = $response['token_type'];
        $accessToken->scope = $response['scope'];
        $accessToken->refreshToken = $response['refresh_token'];

        $accessToken->createdAt = new \DateTime();
        $accessToken->expiresAt = clone($accessToken->createdAt);
        $accessToken->expiresAt->add(\DateInterval::createFromDateString($accessToken->expiresIn . ' seconds'));

        return $accessToken;
    }

    /**
     * @return mixed
     */
    public function getAccessToken ()
    {
        return $this->accessToken;
    }

    /**
     * @param mixed $accessToken
     *
     * @return self
     */
    public function setAccessToken ($accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getExpiresIn ()
    {
        return $this->expiresIn;
    }

    /**
     * @param mixed $expiresIn
     *
     * @return self
     */
    public function setExpiresIn ($expiresIn): self
    {
        $this->expiresIn = $expiresIn;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTokenType ()
    {
        return $this->tokenType;
    }

    /**
     * @param mixed $tokenType
     *
     * @return self
     */
    public function setTokenType ($tokenType): self
    {
        $this->tokenType = $tokenType;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getScope ()
    {
        return $this->scope;
    }

    /**
     * @param mixed $scope
     *
     * @return self
     */
    public function setScope ($scope): self
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRefreshToken ()
    {
        return $this->refreshToken;
    }

    /**
     * @param mixed $refreshToken
     *
     * @return self
     */
    public function setRefreshToken ($refreshToken): self
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt ()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     *
     * @return self
     */
    public function setCreatedAt ($createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getExpiresAt ()
    {
        return $this->expiresAt;
    }

    /**
     * @param mixed $expiresAt
     *
     * @return self
     */
    public function setExpiresAt ($expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }
}
