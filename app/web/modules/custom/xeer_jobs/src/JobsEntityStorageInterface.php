<?php

namespace Drupal\xeer_jobs;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\xeer_jobs\Entity\JobsEntityInterface;

/**
 * Defines the storage handler class for Job position entities.
 *
 * This extends the base storage class, adding required special handling for
 * Job position entities.
 *
 * @ingroup xeer_jobs
 */
interface JobsEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Job position revision IDs for a specific Job position.
   *
   * @param \Drupal\xeer_jobs\Entity\JobsEntityInterface $entity
   *   The Job position entity.
   *
   * @return int[]
   *   Job position revision IDs (in ascending order).
   */
  public function revisionIds(JobsEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Job position author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Job position revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\xeer_jobs\Entity\JobsEntityInterface $entity
   *   The Job position entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(JobsEntityInterface $entity);

  /**
   * Unsets the language for all Job position with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
