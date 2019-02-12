<?php

namespace Drupal\xeer_jobs\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\xeer_jobs\Entity\JobsEntityInterface;

/**
 * Class JobsEntityController.
 *
 *  Returns responses for Job position routes.
 */
class JobsEntityController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Job position  revision.
   *
   * @param int $jobs_entity_revision
   *   The Job position  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($jobs_entity_revision) {
    $jobs_entity = $this->entityManager()->getStorage('jobs_entity')->loadRevision($jobs_entity_revision);
    $view_builder = $this->entityManager()->getViewBuilder('jobs_entity');

    return $view_builder->view($jobs_entity);
  }

  /**
   * Page title callback for a Job position  revision.
   *
   * @param int $jobs_entity_revision
   *   The Job position  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($jobs_entity_revision) {
    $jobs_entity = $this->entityManager()->getStorage('jobs_entity')->loadRevision($jobs_entity_revision);
    return $this->t('Revision of %title from %date', ['%title' => $jobs_entity->label(), '%date' => format_date($jobs_entity->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Job position .
   *
   * @param \Drupal\xeer_jobs\Entity\JobsEntityInterface $jobs_entity
   *   A Job position  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(JobsEntityInterface $jobs_entity) {
    $account = $this->currentUser();
    $langcode = $jobs_entity->language()->getId();
    $langname = $jobs_entity->language()->getName();
    $languages = $jobs_entity->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $jobs_entity_storage = $this->entityManager()->getStorage('jobs_entity');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $jobs_entity->label()]) : $this->t('Revisions for %title', ['%title' => $jobs_entity->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all job position revisions") || $account->hasPermission('administer job position entities')));
    $delete_permission = (($account->hasPermission("delete all job position revisions") || $account->hasPermission('administer job position entities')));

    $rows = [];

    $vids = $jobs_entity_storage->revisionIds($jobs_entity);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\xeer_jobs\JobsEntityInterface $revision */
      $revision = $jobs_entity_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $jobs_entity->getRevisionId()) {
          $link = $this->l($date, new Url('entity.jobs_entity.revision', ['jobs_entity' => $jobs_entity->id(), 'jobs_entity_revision' => $vid]));
        }
        else {
          $link = $jobs_entity->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->getRevisionLogMessage(), '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.jobs_entity.translation_revert', ['jobs_entity' => $jobs_entity->id(), 'jobs_entity_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.jobs_entity.revision_revert', ['jobs_entity' => $jobs_entity->id(), 'jobs_entity_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.jobs_entity.revision_delete', ['jobs_entity' => $jobs_entity->id(), 'jobs_entity_revision' => $vid]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['jobs_entity_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
