<?php

namespace App\Models\Concerns;

/**
 * Frees a HasSlug + SoftDeletes model's slug when the record is soft-deleted, so the same
 * name can be used again without Spatie appending "-1" (and without tripping the DB's
 * UNIQUE(slug) index — MySQL/MariaDB can't do partial "WHERE deleted_at IS NULL" indexes).
 *
 * On soft-delete we rename the trashed row's slug to "{slug}__deleted__{id}", which:
 *   - frees the original slug for a new record,
 *   - keeps the trashed row's slug unique (DB constraint stays happy).
 * On restore Spatie regenerates the slug from the name automatically (and uniquifies it
 * if the original was taken in the meantime), so no extra handling is needed there.
 *
 * Force-deletes are ignored (the row is gone anyway).
 */
trait ReusableSlug
{
    public static function bootReusableSlug(): void
    {
        static::deleted(function ($model) {
            // Skip force-deletes — nothing to free.
            if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
                return;
            }

            $field = $model->getSlugOptions()->slugField;
            $current = $model->{$field};
            if (! is_string($current) || $current === '' || str_contains($current, '__deleted__')) {
                return;
            }

            // Direct update so we don't re-trigger Spatie's slug generation or model events.
            static::withoutGlobalScopes()
                ->whereKey($model->getKey())
                ->update([$field => $current . '__deleted__' . $model->getKey()]);
        });
    }
}
