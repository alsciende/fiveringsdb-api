Feature: use the deckbuilder

  Background:
    Given the database is loaded
    And the cache is cleared

  Scenario: Getting the list of my decks
    Given I have a valid cached token for known user "user"
    When I query "/strains" by GET
    Then I should get a 200 HTTP Response status code
