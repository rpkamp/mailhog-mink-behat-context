Feature: As the developer of this context I want it to function correctly

  Background:
    Given I am on "https://www.github.com/"

  @email
  Scenario: Find link by text and click it
    Given I sent an email with a link
    When I click the link "github" in the last received email
    Then I should be on "https://github.com/rpkamp/mailhog-behat-context"

  @email
  Scenario: Find link by id and click it
    Given I sent an email with a link
    When I click the link "gh-id" in the last received email
    Then I should be on "https://github.com/rpkamp/mailhog-behat-context"

  @email
  Scenario: Find link by title and click it
    Given I sent an email with a link
    When I click the link "gh-title" in the last received email
    Then I should be on "https://github.com/rpkamp/mailhog-behat-context"

  @email
  Scenario: Find link by alt and click it
    Given I sent an email with a link
    When I click the link "gh-alt" in the last received email
    Then I should be on "https://github.com/rpkamp/mailhog-behat-context"

  @email
  Scenario: Find link by alt and click it
    Given I sent an email with a link
    When I click the link "git" in the last received email
    Then I should be on "https://github.com/rpkamp/mailhog-behat-context"
