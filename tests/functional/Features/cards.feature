Feature: get cards data

  Background:
    Given the database is empty
    And the fixtures are loaded

  Scenario: Getting the cards data
    When I query "/cards" by GET
    Then I should get a 200 HTTP Response status code

  Scenario: Getting a card data
    When I query "/cards/above-question" by GET
    Then I should get a 200 HTTP Response status code

  Scenario: Getting the packs data
    When I query "/packs" by GET
    Then I should get a 200 HTTP Response status code

  Scenario: Getting the cycles data
    When I query "/cycles" by GET
    Then I should get a 200 HTTP Response status code

  Scenario: Getting the pack-cards data
    When I query "/pack-cards" by GET
    Then I should get a 200 HTTP Response status code
