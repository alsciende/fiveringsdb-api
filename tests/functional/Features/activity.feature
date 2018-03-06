Feature: use the deckbuilder

  Background:
    Given the database is loaded
    And the cache is cleared

  Scenario: Getting my activity
    Given I have a valid cached token for known user "user"
    When I query "/activity" by GET
    Then I should get a 200 HTTP Response status code

  Scenario: Getting my feed
    Given I have a valid cached token for known user "user"
    When I query "/feed" by GET
    Then I should get a 200 HTTP Response status code

  Scenario: Getting the anonymous feed
    When I query "/feed" by GET
    Then I should get a 200 HTTP Response status code
