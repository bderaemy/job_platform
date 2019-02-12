<?php

namespace Drupal\xeer_jobs\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Job position entities.
 */
class JobsEntityViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
