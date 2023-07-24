<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'photo',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function($o) {
            if(request()->hasFile('photo')) {
                $o->photo = '/storage/' . request()->file('photo')->store('users', 'public');
            }
            $o->password = Hash::make($o->password);
        });

        static::created(function($o) {
            if(request()->post('role') == 1) {
                Writerprofile::create([
                    'user_id' => $o->id,
                    'nickname' => request()->post('nickname'),
                    'principal_gender' => request()->post('principal_gender'),
                    'principal_gender' => request()->post('principal_gender'),
                    'description' => request()->post('description'),
                    'birth_date' => request()->post('birth_date'),
                    'status' => request()->post('status'),
                ]);
            } else if(request()->post('role') == 2) {
                Readerprofile::create([
                    'user_id' => $o->id,
                    'favorite_gender' => request()->post('favorite_gender'),
                    'reading_hours' => request()->post('reading_hours'),
                ]);
            }
        });
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function writerprofile()
    {
        return $this->hasOne(Writerprofile::class);
    }
}
