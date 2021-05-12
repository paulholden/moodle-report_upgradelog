@report @report_upgradelog
Feature: An admin can view the Upgrade log report
  In order to ensure an admin can view the Upgrade log report
  As an admin
  I need to view the Upgrade log report

  @javascript
  Scenario: View the Upgrade log report
    Given I log in as "admin"
    When I navigate to "Reports > Upgrade log" in site administration
    Then I should see "Core installed" in the "generaltable" "table"
