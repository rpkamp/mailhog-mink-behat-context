Feature: As the developer of this context I want it to function correctly

  @email
  Scenario Outline: Find link in latest email and click it
    Given I sent an email with a link
    When I click the link "<link>" in the last received email
    Then I should be on "https://github.com/rpkamp/mailhog-behat-context"

  Examples:
    | link     | description           |
    | github   | Link contents         |
    | gh-id    | Link ID attribute     |
    | gh-title | Link title attribute  |
    | gh-alt   | Link alt attribute    |
    | git      | Partial link contents |

  @email
  Scenario Outline: Open sent email and click the link in it
    Given I sent an email with a link
    When I open the latest email from "me@myself.example"
    And I click the link "<link>" in the opened email
    Then I should be on "https://github.com/rpkamp/mailhog-behat-context"

  Examples:
    | link     | description           |
    | github   | Link contents         |
    | gh-id    | Link ID attribute     |
    | gh-title | Link title attribute  |
    | gh-alt   | Link alt attribute    |
    | git      | Partial link contents |
