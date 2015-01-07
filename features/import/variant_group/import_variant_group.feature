@javascript
Feature: Execute an import
  In order to update existing product information
  As a product manager
  I need to be able to import variant group to create or update them

  Background:
    Given the "footwear" catalog configuration
    And the following product groups:
      | code   | label  | attributes  | type    |
      | SANDAL | Sandal | color, size | VARIANT |
      | NOT_VG | Not VG | color, size | RELATED |
    And I am logged in as "Julia"

  Scenario: Successfully import a csv file of variant group to create a new one
    Given the following CSV file to import:
    """
    code;axis;label-en_US
    NEW_ONE;size,color;"My new VG 1"
    NEW_TWO;color;"My new VG 2"
    """
    And the following job "footwear_variant_group_import" configuration:
      | filePath | %file to import% |
    When I am on the "footwear_variant_group_import" import job page
    And I launch the import job
    And I wait for the "footwear_variant_group_import" job to finish
    Then I should see "Read 2"
    And I should see "Created 2"
    And there should be the following groups:
      | code    | label-en_US | label-fr_FR | attributes | type    |
      | SANDAL  | Sandal      |             | color,size | VARIANT |
      | NOT_VG  | Not VG      |             |            | RELATED |
      | NEW_ONE | My new VG 1 |             | color,size | VARIANT |
      | NEW_TWO | My new VG 2 |             | color      | VARIANT |

  Scenario: Successfully import a csv file of variant group to update an existing one
    Given the following CSV file to import:
    """
    code;axis;label-en_US
    SANDAL;size,color;"My new label"
    """
    And the following job "footwear_variant_group_import" configuration:
      | filePath | %file to import% |
    When I am on the "footwear_variant_group_import" import job page
    And I launch the import job
    And I wait for the "footwear_variant_group_import" job to finish
    Then I should see "Read 1"
    And I should see "Updated 1"
    And there should be the following groups:
      | code    | label-en_US  | label-fr_FR | attributes | type    |
      | SANDAL  | My new label |             | color,size | VARIANT |
      | NOT_VG  | Not VG       |             |            | RELATED |

  Scenario: Fail to import variant group with updated axis (here we try to change color and size to color)
    Given the following CSV file to import:
    """
    code;axis;label-en_US
    SANDAL;size;"Sandal"
    """
    And the following job "footwear_variant_group_import" configuration:
      | filePath | %file to import% |
    When I am on the "footwear_variant_group_import" import job page
    And I launch the import job
    And I wait for the "footwear_variant_group_import" job to finish
    Then I should see "Read 1"
    And I should see "Updated 1"
    And there should be the following groups:
      | code    | label-en_US | label-fr_FR | attributes | type    |
      | SANDAL  | Sandal      |             | color,size | VARIANT |
      | NOT_VG  | Not VG      |             |            | RELATED |

  Scenario: Stop the import when encounter a new variant group with no axis
    Given the following CSV file to import:
    """
    code;axis;label-en_US
    NO_AXIS;;"My VG with no axis"
    """
    And the following job "footwear_variant_group_import" configuration:
      | filePath | %file to import% |
    When I am on the "footwear_variant_group_import" import job page
    And I launch the import job
    And I wait for the "footwear_variant_group_import" job to finish
    Then I should see "Status: Failed"
    And I should see "Axis must be provided for the new variant group \"NO_AXIS\""
    And I should see "Read 1"
    And there should be the following groups:
      | code    | label-en_US | label-fr_FR | attributes | type    |
      | SANDAL  | Sandal      |             | color,size | VARIANT |
      | NOT_VG  | Not VG      |             |            | RELATED |

  Scenario: Skip the line when encounter an existing group which is not a variant group
    Given the following CSV file to import:
    """
    code;axis;label-en_US
    NOT_VG;;"My standard not updatable group"
    """
    And the following job "footwear_variant_group_import" configuration:
      | filePath | %file to import% |
    When I am on the "footwear_variant_group_import" import job page
    And I launch the import job
    And I wait for the "footwear_variant_group_import" job to finish
    Then I should see "Variant group \"NOT_VG\" does not exist"
    And I should see "Read 1"
    And I should see "Skipped 1"
    And there should be the following groups:
      | code    | label-en_US | label-fr_FR | attributes | type    |
      | SANDAL  | Sandal      |             | color,size | VARIANT |
      | NOT_VG  | Not VG      |             |            | RELATED |