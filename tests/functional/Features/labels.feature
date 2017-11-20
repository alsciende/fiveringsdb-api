Feature: get the i18n labels

  Background:
    Given the database is loaded

  Scenario: Getting all labels for the default locale
    When I query "/labels" by GET
    Then I should get a 200 HTTP Response status code
