Feature: Developer runs commands to rebase Pull Request
  As a Developer
  I want to keep my PR rebased
  In order to get it merged asap

  Scenario: Rebasing a Pull Request
    Given I have a PR into a repo that is not rebased
     When I run the command to rebase a PR
     Then I should see "PR rebased"
