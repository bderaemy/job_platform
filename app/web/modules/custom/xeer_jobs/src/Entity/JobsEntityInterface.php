<?php

namespace Drupal\xeer_jobs\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Job position entities.
 *
 * @ingroup xeer_jobs
 */
interface JobsEntityInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Job position name.
   *
   * @return string
   *   Name of the Job position.
   */
  public function getName();

  /**
   * Sets the Job position name.
   *
   * @param string $name
   *   The Job position name.
   *
   * @return \Drupal\xeer_jobs\Entity\JobsEntityInterface
   *   The called Job position entity.
   */
  public function setName($name);

  /**
   * Gets the Job position creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Job position.
   */
  public function getCreatedTime();

  /**
   * Sets the Job position creation timestamp.
   *
   * @param int $timestamp
   *   The Job position creation timestamp.
   *
   * @return \Drupal\xeer_jobs\Entity\JobsEntityInterface
   *   The called Job position entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Job position published status indicator.
   *
   * Unpublished Job position are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Job position is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Job position.
   *
   * @param bool $published
   *   TRUE to set this Job position to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\xeer_jobs\Entity\JobsEntityInterface
   *   The called Job position entity.
   */
  public function setPublished($published);

  /**
   * Gets the Job position revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Job position revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\xeer_jobs\Entity\JobsEntityInterface
   *   The called Job position entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Job position revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Job position revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\xeer_jobs\Entity\JobsEntityInterface
   *   The called Job position entity.
   */
  public function setRevisionUserId($uid);

}
