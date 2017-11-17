Feature: load the decks of the week

  Background:
    Given the database is loaded

  Scenario: Getting the decks of the week
    When I query "/features" by GET
    Then I should get a 200 HTTP Response status code
