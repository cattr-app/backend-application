<?php

namespace App\Models;

use App\Scopes\ProjectScope;
use App\Traits\ExposePermissions;
use Eloquent as EloquentIdeHelper;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Carbon;

/**
 * @apiDefine ProjectObject
 *
 * @apiSuccess {Integer}  project.id            ID
 * @apiSuccess {Integer}  [project.company_id]  Company ID
 * @apiSuccess {String}   project.name          Name
 * @apiSuccess {String}   project.description   Description of project
 * @apiSuccess {ISO8601}  project.created_at    Creation DateTime
 * @apiSuccess {ISO8601}  project.updated_at    Update DateTime
 * @apiSuccess {ISO8601}  project.deleted_at    Delete DateTime or `NULL` if wasn't deleted
 * @apiSuccess {Array}    [project.users]       Users attached to project
 * @apiSuccess {Array}    [project.tasks]       Tasks of project
 *
 * @apiVersion 1.0.0
 */

/**
 * App\Models\Project
 *
 * @apiDefine ProjectParams
 * @apiParam {Integer}  [id]           ID
 * @apiParam {Integer}  [company_id]   Company ID
 * @apiParam {String}   [name]         Name
 * @apiParam {String}   [description]  Description of project
 * @apiParam {ISO8601}  [created_at]   Creation DateTime
 * @apiParam {ISO8601}  [updated_at]   Update DateTime
 * @apiParam {ISO8601}  [deleted_at]   Delete DateTime or `NULL` if user wasn't deleted
 * @apiParam {String}   [with]         Related models to return in response
 * @apiParam {Object}   [users]        Users attached to project,all params in <a href="#api-User-GetUserList">@User</a>
 * @apiParam {Object}   [tasks]        Tasks of project, all params in <a href="#api-Task-GetTaskList" >@Task</a>
 * @apiVersion 1.0.0
 * @property int                                $id
 * @property int|null                           $company_id
 * @property string                 $name
 * @property string                 $description
 * @property Carbon|null            $deleted_at
 * @property Carbon|null            $created_at
 * @property Carbon|null            $updated_at
 * @property int                    $important
 * @property string                 $source
 * @property int|null               $default_priority_id
 * @property-read Priority|null     $defaultPriority
 * @property-read array             $can
 * @property-read Collection|Role[] $roles
 * @property-read int|null          $roles_count
 * @property-read Collection|Task[] $tasks
 * @property-read int|null          $tasks_count
 * @property-read Collection|User[] $users
 * @property-read int|null          $users_count
 * @method static EloquentBuilder|Project newModelQuery()
 * @method static EloquentBuilder|Project newQuery()
 * @method static QueryBuilder|Project onlyTrashed()
 * @method static EloquentBuilder|Project query()
 * @method static EloquentBuilder|Project whereCompanyId($value)
 * @method static EloquentBuilder|Project whereCreatedAt($value)
 * @method static EloquentBuilder|Project whereDefaultPriorityId($value)
 * @method static EloquentBuilder|Project whereDeletedAt($value)
 * @method static EloquentBuilder|Project whereDescription($value)
 * @method static EloquentBuilder|Project whereId($value)
 * @method static EloquentBuilder|Project whereImportant($value)
 * @method static EloquentBuilder|Project whereName($value)
 * @method static EloquentBuilder|Project whereSource($value)
 * @method static EloquentBuilder|Project whereUpdatedAt($value)
 * @method static QueryBuilder|Project withTrashed()
 * @method static QueryBuilder|Project withoutTrashed()
 * @mixin EloquentIdeHelper
 * @property-read Collection|Status[] $statuses
 * @property-read int|null            $statuses_count
 * @method static \Database\Factories\ProjectFactory factory(...$parameters)
 */

class Project extends Model
{
    use SoftDeletes;
    use ExposePermissions;
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'company_id',
        'name',
        'description',
        'important',
        'source',
        'default_priority_id',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'company_id' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'important' => 'integer',
        'source' => 'string',
        'default_priority_id' => 'integer',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    /**
     * @var array
     */
    protected $appends = ['can'];

    /**
     * @var array
     */
    protected $permissions = [
        'update',
        'update_members',
        'destroy',
    ];

    /**
     * Override parent boot and Call deleting event
     */
    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new ProjectScope);

        static::deleting(static function (Project $project) {
            $project->tasks()->delete();
        });
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'project_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'projects_users')
            ->withPivot('role_id')
            ->using(ProjectUserPivot::class)
            ->withoutGlobalScopes();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'projects_roles', 'project_id', 'role_id');
    }

    public function defaultPriority(): HasOne
    {
        return $this->hasOne(Priority::class, 'id', 'default_priority_id');
    }

    /**
     * @return BelongsToMany
     */
    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class, 'projects_statuses', 'project_id', 'status_id')->withPivot('color');
    }

    public function getNameAttribute(): string
    {
        return empty($this->attributes['source']) || $this->attributes['source'] === 'internal'
            ? $this->attributes['name']
            : ucfirst($this->attributes['source']) . ": {$this->attributes['name']}";
    }
}
