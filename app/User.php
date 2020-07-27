<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use SoftDeletes;
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'phone', 'name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'created_at', 'updated_at', 'roleId', 'role'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    // protected $appends = ['account'];
    // public function getAccountAttribute()
    // {
    //     return $this->attributes['account'] = $this->account();
    // }

    public function account()
    {
        return $this->attributes['account'] = $this->hasOne('App\Account', 'userId', 'id');
    }
    public function role()
    {
        return $this->attributes['role'] = $this->belongsTo('App\Role', 'roleId', 'id');
    }

    public function receivedTransfer()
    {
        return $this->attributes['receivedTransfer'] = $this->hasManyThrough('App\Transfer','App\Account','userId', 'receivedId', 'id', 'accountNumber');
    }

    public function sendTransfer()
    {
        return $this->attributes['sendTransfer'] = $this->hasManyThrough('App\Transfer','App\Account','userId','sendId', 'id', 'accountNumber');
    }

    public function remembers()
    {
        return $this->attributes['remember'] = $this->hasMany('App\RememberList', 'ownerId', 'id');
    }
    public function setPasswordAttribute($password)
    {
        if (!empty($password)) {
            // dd($password);
            $this->attributes['password'] = bcrypt($password);
        }
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

    public function authorizeRoles($roles)
    {
        if (is_array($roles)) {
            return $this->hasAnyRole($roles) ||
                abort(401, 'This action is unauthorized.');
        }
        return $this->hasRole($roles) ||
            abort(401, 'This action is unauthorized.');
    }
    /**
     * Check multiple roles
     * @param array $roles
     */
    public function hasAnyRole($roles)
    {
        return null !== $this->roles()->whereIn('name', $roles)->first();
    }
    /**
     * Check one role
     * @param string $role
     */
    public function hasRole($role)
    {
        return null !== $this->roles()->where('name', $role)->first();
    }
}
