<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EavAttribute
 *
 * @property int $attribute_id
 * @property int $attribute_group_id
 * @property int $entity_type_id
 * @property string $attribute_name
 * @property string $data_type String, integer, datetime...
 * @property string $frontend_tye text, number, picklist...
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EavAttribute whereAttributeGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EavAttribute whereAttributeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EavAttribute whereAttributeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EavAttribute whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EavAttribute whereDataType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EavAttribute whereEntityTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EavAttribute whereFrontendTye($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EavAttribute whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EavAttribute extends Model
{
    protected $table = 'eav_attribute';
}
