<?php

namespace Drupal\xeer_jobs;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Job position entity.
 *
 * @see \Drupal\xeer_jobs\Entity\JobsEntity.
 */
class JobsEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\xeer_jobs\Entity\JobsEntityInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          if ($entity->getOwnerId() === $account->id()) {
            return AccessResult::allowedIfHasPermission($account, 'view own unpublished job position entities');
          }
          return AccessResult::allowedIfHasPermission($account, 'view unpublished job position entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published job position entities');

      case 'update':
        if ($entity->getOwnerId() === $account->id()) {
          return AccessResult::allowedIfHasPermission($account, 'edit own job position entity');
        }
        return AccessResult::allowedIfHasPermission($account, 'edit job position entities');

      case 'delete':
        if ($entity->getOwnerId() === $account->id()) {
          return AccessResult::allowedIfHasPermission($account, 'edit own job position entity');
        }
        return AccessResult::allowedIfHasPermission($account, 'delete job position entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add job position entities');
  }

}
