<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements CanResetPassword
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'login',
        'email',
        'password',
        'role',
        'must_change_password',
    ];
    protected $guarded = [];

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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isModerator()
    {
        return $this->role === 'moderator';
    }

    public function user_points()
    {
        return $this->hasMany(UserPoint::class);
    }

    public function points()
    {
        return $this->hasMany(UserPoint::class);
    }

    public function friends(): HasMany
    {
        return $this->hasMany(Friend::class, 'user_id', 'id');
    }

    public function getEmailForPasswordReset()
    {
        return $this->email_address;
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'user_task')->withPivot('completed');
    }

    public function completedTasks()
    {
        return $this->belongsToMany(Task::class, 'user_task')
            ->withPivot('completed')
            ->withTimestamps();
    }

    public function countcompletedTasks()
    {
        return $this->belongsToMany(Task::class, 'user_task')
            ->withPivot('completed')
            ->wherePivot('completed', true);
    }







}
