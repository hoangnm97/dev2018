<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AttributeGroup
 *
 * @property int $attribute_group_id
 * @property int $entity_type_id
 * @property string $attribute_group_name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttributeGroup whereAttributeGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttributeGroup whereAttributeGroupName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttributeGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttributeGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttributeGroup whereEntityTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AttributeGroup whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AttributeGroup extends Model
{
    protected $table = 'attribute_group';
}
