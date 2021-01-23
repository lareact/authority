<?php


namespace Golly\Authority\Eloquent\Traits;

use Illuminate\Support\Str;

/**
 * Trait HasUuid
 * @package Golly\Authority\Eloquent\Traits
 * @mixin \Golly\Authority\Eloquent\Model;
 */
trait HasUuid
{

    /**
     * init uuid
     *
     * @return void
     */
    public static function bootHasUuid()
    {
        self::creating(function ($model) {
            if (isset($model->uuidFields)) {
                foreach ((array)$model->uuidFields as $field) {
                    if (!$model->{$field}) {
                        $model->{$field} = Str::uuid()->toString();
                    }
                }
            }
            if (empty($model->{$model->primaryKey}) && $model->keyType == 'string') {
                $model->{$model->primaryKey} = Str::uuid()->toString();
            }
        });
    }
}
