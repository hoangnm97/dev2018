<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EntityType
 *
 * @property int $entity_type_id
 * @property string $entity_type_name type: lead, product,...
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EntityType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EntityType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EntityType whereEntityTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EntityType whereEntityTypeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EntityType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EntityType extends Model
{
    protected $table = 'entity_type';
}
