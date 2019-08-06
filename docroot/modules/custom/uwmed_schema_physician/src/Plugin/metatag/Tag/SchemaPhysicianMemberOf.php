<?php

namespace Drupal\uwmed_schema_physician\Plugin\metatag\Tag;

use Drupal\schema_metatag\Plugin\metatag\Tag\SchemaPersonOrgBase;

/**
 * Provides a plugin for the 'schema_physician_member_of' meta tag.
 *
 * - 'id' should be a globally unique id.
 * - 'name' should match the Schema.org element name.
 * - 'group' should match the id of the group that defines the Schema.org type.
 *
 * @MetatagTag(
 *   id = "schema_physician_member_of",
 *   label = @Translation("Member Of"),
 *   description = @Translation("An Organization (or ProgramMembership) to which this Physician belongs."),
 *   name = "memberOf",
 *   group = "schema_physician",
 *   weight = 11,
 *   type = "string",
 *   secure = FALSE,
 *   multiple = FALSE
 * )
 */
class SchemaPhysicianMemberOf extends SchemaPersonOrgBase {
  // Nothing here yet. Just a placeholder class for a plugin.
}
