<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LeadEntity
 *
 * @property int $lead_id
 * @property int $entity_id
 * @property int $entity_type_id
 * @property string $name
 * @property string $phone
 * @property string $email
 * @property int $created_by
 * @property int $assigned_to
 * @property int $lead_status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LeadEntity whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LeadEntity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LeadEntity whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LeadEntity whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LeadEntity whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LeadEntity whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LeadEntity whereEntityTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LeadEntity whereLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LeadEntity whereLeadStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LeadEntity whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LeadEntity wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LeadEntity whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LeadEntity extends Model
{
    protected $table = 'lead_entity';
}
