Feature: search decks

  Background:
    Given the database is loaded

  Scenario: Getting decks sorted by date
    When I query "/decks?sort=date" by GET
    Then I should get a 200 HTTP Response status code
    And  The response should be successful

  Scenario: Getting decks sorted by popularity from a date
    When I query "/decks?sort=popularity&since=2017-11-01" by GET
    Then I should get a 200 HTTP Response status code
    And  The response should be successful

  Scenario: Getting decks from decks of the week
    When I query "/decks?sort=date&featured=yes" by GET
    Then I should get a 200 HTTP Response status code
    And  The response should be successful

  Scenario: Getting decks from a multiple criterias
    When I query "/decks?sort=popularity&since=2017-11-01&clan=dragon" by GET
    Then I should get a 200 HTTP Response status code
    And  The response should be successful

  Scenario: Getting trending decks
    When I query "/decks?sort=trending" by GET
    Then I should get a 200 HTTP Response status code
    And  The response should be successful


