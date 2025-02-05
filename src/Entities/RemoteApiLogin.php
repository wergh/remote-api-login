<?php

namespace Wergh\RemoteApiLogin\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;


/**
 * Class RemoteApiLogin
 *
 * @package App\Models
 * @version February 01, 2025, 10:14 pm UTC
 *
 * @property string $uuid
 * @property string $code
 * @property string $token
 * @property string $authenticatable_type
 * @property integer $authenticatable_id
 */
class RemoteApiLogin extends Model
{

    protected $table;

    public $fillable = [
        'uuid',
        'code',
        'token',
    ];

    protected $casts = [
        'uuid' => 'string',
        'code' => 'string',
        'token' => 'string',
    ];

    /**
     * Create a new model instance.
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = Config::get('remote-api-login.table_name');
    }


    public static function newData(): array
    {
        $letters = 'ABCDEFGHIJKLMNPQRSTUVWXYZ';
        $code = substr(str_shuffle(str_repeat($letters, ceil(16 / strlen($letters)))), 0, Config::get('remote-api-login.code_length'));
        if (self::searchByCode($code)) {
            return self::newData();
        }
        return [
            'uuid' => Str::uuid(),
            'code' => $code,
            'token' => Str::random(Config::get('remote-api-login.token_length'))
        ];
    }

    public static function searchByCode(string $code): RemoteApiLogin|null
    {
        return self::where('code', $code)
            ->where('created_at', '>', Carbon::now()->subSeconds(Config::get('remote-api-login.expiration_time_in_seconds')))
            ->first();
    }

    public static function searchByUuidAndToken(string $uuid, string $token): RemoteApiLogin|null
    {
        return self::where('uuid', $uuid)
            ->where('token', $token)
            ->where('created_at', '>', Carbon::now()->subSeconds(Config::get('remote-api-login.expiration_time_in_seconds')))
            ->first();
    }

    public function authenticatable(): MorphTo
    {
        return $this->morphTo();
    }

}
