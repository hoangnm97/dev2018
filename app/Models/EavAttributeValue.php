<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EavAttributeValue
 *
 * @property int $attribute_value_id
 * @property int $attribute_id
 * @property int $entity_id
 * @property string $value value of attribute
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EavAttributeValue whereAttributeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EavAttributeValue whereAttributeValueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EavAttributeValue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EavAttributeValue whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EavAttributeValue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EavAttributeValue whereValue($value)
 * @mixin \Eloquent
 */
class EavAttributeValue extends Model
{
    protected $table = 'eav_attribute_value';
}
