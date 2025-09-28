<?php

namespace Jundayw\Tokenable\Models;

use Carbon\CarbonImmutable;
use DateTimeImmutable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Jundayw\Tokenable\Concerns\Auth\Authenticatable;
use Jundayw\Tokenable\Contracts\Auth\Authenticable;

class Authentication extends Model implements Authenticable
{
    use SoftDeletes;
    use Authenticatable;

    /**
     * The name of the "created at" column.
     *
     * @var string|null
     */
    const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string|null
     */
    const UPDATED_AT = 'updated_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string|null
     */
    const DELETED_AT = 'deleted_at';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'scopes'                     => 'array',
        'access_token_expire_at'     => 'datetime',
        'refresh_token_available_at' => 'datetime',
        'refresh_token_expire_at'    => 'datetime',
        'last_used_at'               => 'datetime',
        'created_at'                 => 'datetime',
        'updated_at'                 => 'datetime',
        'deleted_at'                 => 'datetime',
    ];

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]|bool
     */
    protected $guarded = [];

    /**
     * Get the current connection name for the model.
     *
     * @return string|null
     */
    public function getConnectionName(): ?string
    {
        return $this->connection ?? config('tokenable.database.connection');
    }

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->table ?? config('tokenable.database.table', parent::getTable());
    }

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param DateTimeInterface $date
     *
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date instanceof DateTimeImmutable ?
            CarbonImmutable::instance($date)->toIso8601ZuluString() :
            Carbon::instance($date)->toIso8601ZuluString();
    }

    /**
     * Define a getter for the platform property.
     *
     * @return Attribute
     */
    protected function platform(): Attribute
    {
        return new Attribute(
            set: fn($value, $attributes) => strtolower($value),
        );
    }
}
