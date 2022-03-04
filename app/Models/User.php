<?php

namespace App\Models;

use App\Enums\UserType;
use App\Mail\ResetPassword;
use App\Scopes\UserScope;
use App\Traits\HasRole;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Eloquent as EloquentIdeHelper;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Hash;
use Laravel\Sanctum\HasApiTokens;
use Mpdf\Tag\A;

/**
 * @apiDefine UserObject
 *
 * @apiSuccess {Integer}  user.id                       ID
 * @apiSuccess {String}   user.full_name                Name
 * @apiSuccess {String}   user.email                    Email
 * @apiSuccess {Integer}  user.company_id               Company ID
 * @apiSuccess {String}   user.avatar                   Avatar image url
 * @apiSuccess {Boolean}  user.screenshots_active       Should screenshots be captured
 * @apiSuccess {Boolean}  user.manual_time              Allow manual time edit
 * @apiSuccess {Integer}  user.screenshots_interval     Screenshots capture interval (seconds)
 * @apiSuccess {Boolean}  user.active                   Indicates active user when `TRUE`
 * @apiSuccess {String}   user.timezone                 User's timezone
 * @apiSuccess {ISO8601}  user.created_at               Creation DateTime
 * @apiSuccess {ISO8601}  user.updated_at               Update DateTime
 * @apiSuccess {ISO8601}  user.deleted_at               Delete DateTime or `NULL` if wasn't deleted
 * @apiSuccess {String}   user.url                     `Not used`
 * @apiSuccess {Boolean}  user.computer_time_popup     `Not used`
 * @apiSuccess {Boolean}  user.blur_screenshots        `Not used`
 * @apiSuccess {Boolean}  user.web_and_app_monitoring  `Not used`
 * @apiSuccess {String}   user.user_language            Language which is used for frontend translations and emails
 *
 * @apiVersion 1.0.0
 */

/**
 * @apiDefine UserParams
 *
 * @apiParam {Integer}  [id]                       ID
 * @apiParam {String}   [full_name]                Name
 * @apiParam {String}   [email]                    Email
 * @apiParam {Integer}  [company_id]               Company ID
 * @apiParam {String}   [avatar]                   Avatar image url
 * @apiParam {Boolean}  [screenshots_active]       Should screenshots be captured
 * @apiParam {Boolean}  [manual_time]              Allow manual time edit
 * @apiParam {Integer}  [screenshots_interval]     Screenshots capture interval (seconds)
 * @apiParam {Boolean}  [active]                   Indicates active user when `TRUE`
 * @apiParam {String}   [timezone]                 User's timezone
 * @apiParam {ISO8601}  [created_at]               Creation DateTime
 * @apiParam {ISO8601}  [updated_at]               Update DateTime
 * @apiParam {ISO8601}  [deleted_at]               Delete DateTime
 * @apiParam {String}   [url]                     `Not used`
 * @apiParam {Boolean}  [computer_time_popup]     `Not used`
 * @apiParam {Boolean}  [blur_screenshots]        `Not used`
 * @apiParam {Boolean}  [web_and_app_monitoring]  `Not used`
 * @apiParam {String}   [user_language]            Language which is used for frontend translations and emails
 *
 * @apiVersion 1.0.0
 */

/**
 * @apiDefine UserScopedParams
 *
 * @apiParam {Integer}  [users.id]                       ID
 * @apiParam {String}   [users.full_name]                Name
 * @apiParam {String}   [users.email]                    Email
 * @apiParam {Integer}  [users.company_id]               Company ID
 * @apiParam {String}   [users.avatar]                   Avatar image url
 * @apiParam {Boolean}  [users.screenshots_active]       Should screenshots be captured
 * @apiParam {Boolean}  [users.manual_time]              Allow manual time edit
 * @apiParam {Integer}  [users.screenshots_interval]     Screenshots capture interval (seconds)
 * @apiParam {Boolean}  [users.active]                   Indicates active user when `TRUE`
 * @apiParam {String}   [users.timezone]                 User's timezone
 * @apiParam {ISO8601}  [users.created_at]               Creation DateTime
 * @apiParam {ISO8601}  [users.updated_at]               Update DateTime
 * @apiParam {ISO8601}  [users.deleted_at]               Delete DateTime
 * @apiParam {String}   [users.url]                     `Not used`
 * @apiParam {Boolean}  [users.computer_time_popup]     `Not used`
 * @apiParam {Boolean}  [users.blur_screenshots]        `Not used`
 * @apiParam {Boolean}  [users.web_and_app_monitoring]  `Not used`
 * @apiParam {String}   [users.user_language]            Language which is used for frontend translations and emails
 *
 * @apiVersion 1.0.0
 */


/**
 * App\Models\User
 *
 * @property int $id
 * @property string $full_name
 * @property string $email
 * @property int|null $interval_duration
 * @property bool $active
 * @property string $password
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $timezone
 * @property int $role_id
 * @property string $locale
 * @property UserType $type
 * @property int $invitation_sent
 * @property int $client_installed
 * @property int $permanent_screenshots
 * @property \Illuminate\Support\Carbon $last_activity
 * @property int $interval_proof_methods
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|\App\Models\Project[] $projects
 * @property-read int|null $projects_count
 * @property-read Collection|\App\Models\ProjectsUsers[] $projectsRelation
 * @property-read int|null $projects_relation_count
 * @property-read Collection|\App\Models\Property[] $properties
 * @property-read int|null $properties_count
 * @property-read \App\Models\Role $role
 * @property-read Collection|\App\Models\Task[] $tasks
 * @property-read int|null $tasks_count
 * @property-read Collection|\App\Models\TimeInterval[] $timeIntervals
 * @property-read int|null $time_intervals_count
 * @property-read Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static EloquentBuilder|User newModelQuery()
 * @method static EloquentBuilder|User newQuery()
 * @method static QueryBuilder|User onlyTrashed()
 * @method static EloquentBuilder|User query()
 * @method static EloquentBuilder|User whereActive($value)
 * @method static EloquentBuilder|User whereClientInstalled($value)
 * @method static EloquentBuilder|User whereCreatedAt($value)
 * @method static EloquentBuilder|User whereDeletedAt($value)
 * @method static EloquentBuilder|User whereEmail($value)
 * @method static EloquentBuilder|User whereFullName($value)
 * @method static EloquentBuilder|User whereId($value)
 * @method static EloquentBuilder|User whereIntervalDuration($value)
 * @method static EloquentBuilder|User whereIntervalProofMethods($value)
 * @method static EloquentBuilder|User whereInvitationSent($value)
 * @method static EloquentBuilder|User whereLastActivity($value)
 * @method static EloquentBuilder|User whereLocale($value)
 * @method static EloquentBuilder|User wherePassword($value)
 * @method static EloquentBuilder|User wherePermanentScreenshots($value)
 * @method static EloquentBuilder|User whereRoleId($value)
 * @method static EloquentBuilder|User whereTimezone($value)
 * @method static EloquentBuilder|User whereType($value)
 * @method static EloquentBuilder|User whereUpdatedAt($value)
 * @method static QueryBuilder|User withTrashed()
 * @method static QueryBuilder|User withoutTrashed()
 * @mixin EloquentIdeHelper
 */
class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    use HasRole;
    use HasFactory;
    use HasApiTokens;

    /**
     * @var array
     */
    protected $with = [
        'role',
        'projectsRelation.role',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_name',
        'email',
        'screenshots_active',
        'manual_time',
        'active',
        'password',
        'timezone',
        'role_id',
        'locale',
        'type',
        'invitation_sent',
        'client_installed',
        'last_activity',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'type' => UserType::class,
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'last_activity',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new UserScope);
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'online',
    ];

    /**
     * @return BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'projects_users', 'user_id', 'project_id')
            ->withPivot('role_id');
    }

    /**
     * @return HasMany
     */
    public function projectsRelation(): HasMany
    {
        return $this->hasMany(ProjectsUsers::class, 'user_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'tasks_users', 'user_id', 'task_id');
    }

    /**
     * @return HasMany
     */
    public function timeIntervals(): HasMany
    {
        return $this->hasMany(TimeInterval::class, 'user_id');
    }

    /**
     * @return HasMany
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'entity_id')
            ->where('entity_type', Property::USER_CODE);
    }

    /**
     * Send the password reset notification.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPassword($this->email, $token));
    }

    protected function online(): Attribute
    {
        return Attribute::get(
            static fn($value, $attributes) => isset($attributes['last_activity']) &&
                $attributes['last_activity']->diffInSeconds(Carbon::now()) < config('app.user_activity.online_status_time')
        );
    }

    protected function password(): Attribute
    {
        return Attribute::set(static fn(string $value) => Hash::needsRehash($value) ? Hash::make($value) : $value);
    }
}
