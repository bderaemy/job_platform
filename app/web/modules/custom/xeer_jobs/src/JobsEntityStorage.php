<?php

namespace Drupal\xeer_jobs;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
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
class JobsEntityStorage extends SqlContentEntityStorage implements JobsEntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(JobsEntityInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {jobs_entity_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {jobs_entity_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(JobsEntityInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {jobs_entity_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('jobs_entity_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
