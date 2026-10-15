<?php

declare(strict_types=1);

namespace Drupal\trash\File;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Entity\TranslatableInterface;
use Drupal\Core\Field\EntityReferenceFieldItemListInterface;

/**
 * Provides file entity integration for trash handlers.
 *
 * Trash does not delete referenced files when an entity is trashed; that is
 * left to the caller (a user action or a module such as media_file_delete).
 * When it does happen in the same request, the files share the entity's
 * deletion timestamp, and this trait uses that to bring them back on restore.
 */
trait FileIntegrationTrait {

  /**
   * Whether file entities should be handled for the given entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to check.
   *
   * @return bool
   *   TRUE if file entities should be handled, FALSE otherwise.
   */
  protected function shouldHandleFiles(EntityInterface $entity): bool {
    return $entity instanceof FieldableEntityInterface
      && $entity->getEntityTypeId() !== 'file'
      && $this->trashManager->isEntityTypeEnabled('file');
  }

  /**
   * Returns the file entity IDs referenced by any translation of the entity.
   *
   * Every entity reference field targeting files counts, including base
   * fields such as the media thumbnail, so a shared thumbnail file that was
   * trashed together with the entity comes back with it.
   *
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   *   The entity whose fields to inspect.
   *
   * @return int[]
   *   The referenced file IDs, without duplicates.
   */
  protected function getReferencedFileIds(FieldableEntityInterface $entity): array {
    $translations = $entity instanceof TranslatableInterface
      ? array_map($entity->getTranslation(...), array_keys($entity->getTranslationLanguages()))
      : [$entity];

    $file_ids = [];
    foreach ($translations as $translation) {
      foreach ($translation->getFields() as $field) {
        if (!$field instanceof EntityReferenceFieldItemListInterface
          || $field->getFieldDefinition()->getSetting('target_type') !== 'file') {
          continue;
        }
        foreach ($field as $item) {
          if ($item->target_id !== NULL) {
            $file_ids[] = (int) $item->target_id;
          }
        }
      }
    }

    return array_values(array_unique($file_ids));
  }

  /**
   * Restores files that were trashed together with the entity.
   *
   * A file is restored when the entity references it and its deletion
   * timestamp matches the entity's. Files trashed on their own keep a
   * different timestamp and stay in the trash.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity being restored.
   * @param int|string $deleted_timestamp
   *   The timestamp when the entity was deleted.
   */
  protected function restoreAssociatedFiles(EntityInterface $entity, int|string $deleted_timestamp): void {
    if (!$this->shouldHandleFiles($entity)) {
      return;
    }

    assert($entity instanceof FieldableEntityInterface);
    $file_ids = $this->getReferencedFileIds($entity);
    if ($file_ids === []) {
      return;
    }

    $file_storage = $this->entityTypeManager->getStorage('file');
    $ids = $file_storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('fid', $file_ids, 'IN')
      ->condition('deleted', $deleted_timestamp)
      ->execute();
    if (empty($ids)) {
      return;
    }

    $files = $this->trashManager->executeInTrashContext('ignore', function () use ($file_storage, $ids) {
      return $file_storage->loadMultiple($ids);
    });
    $file_storage->restoreFromTrash(array_values($files));
  }

}
