<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
        'role',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function projectsOwned()
    {
        return $this->hasMany(Project::class, 'owner_id');
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_users')
            ->withPivot('role', 'added_at')
            ->withTimestamps();
    }

    public function projectUsers()
    {
        return $this->hasMany(ProjectUser::class);
    }
}
