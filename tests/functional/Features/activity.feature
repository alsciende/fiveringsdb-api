Feature: use the deckbuilder

  Background:
    Given the database is loaded

  Scenario: Getting the list of my decks
    Given I am authenticated as user "user"
    When I query "/activity" by GET
    Then I should get a 200 HTTP Response status code

  Scenario: Getting the list of my decks
    Given I am authenticated as user "user"
    When I query "/feed" by GET
    Then I should get a 200 HTTP Response status code
