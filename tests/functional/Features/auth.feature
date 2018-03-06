Feature: authenticating with access tokens

  Background:
    Given the database is loaded
    And the cache is cleared

  Scenario: Accessing private data with a cached valid token for a known user
    Given I have a valid cached token for known user "user"
    When I query "/users/me" by GET
    Then I should get a 200 HTTP Response status code

  Scenario: Accessing private data with an invalid token
    Given I have an invalid token
    When I query "/users/me" by GET
    Then I should get a 403 HTTP Response status code

  Scenario: Accessing private data with an uncached valid token for a known user
    Given I have a valid uncached token for user "user"
    When I query "/users/me" by GET
    Then I should get a 200 HTTP Response status code
    And my token should be cached

  Scenario: Accessing private data with an uncached valid token for an unknown user
    Given I have a valid uncached token for unknown user "new"
    When I query "/users/me" by GET
    Then I should get a 200 HTTP Response status code
    And my token should be cached

