Feature: Monitor catalog volume
  In order to guarantee the performance of the PIM
  As an administrator user
  I want to monitor the volume of products

  @acceptance
  Scenario: Monitor the number of products
    Given a catalog with 10 products
    When the administrator user asks for the report to monitor the number of products
    Then the report returns that the number of products is 10

  @acceptance
  Scenario: Warn the user administrator when the maximum number of products is high
    Given a catalog with 11 products
    And the limit of the number of products is set to 10
    When the administrator user asks for the report to monitor the number of products
    Then the report warns the users that the number of products is high
