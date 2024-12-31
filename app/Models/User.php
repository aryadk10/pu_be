<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'profile_picture', // Foto profil
        'name',       // Nama depan
        'lastname',        // Nama belakang
        'gender',          // Jenis kelamin
        'email',           // Alamat email
        'phone',           // Nomor telepon
        'role',            // Role (admin atau user)
        'password',        // Kata sandi
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
    ];

    // Which fields can be selected from the database through the query string
    public function getAllowedFields(): array
    {
        // Your implementation here
        return [
            'profile_picture', // Foto profil
            'name',       // Nama depan
            'lastname',        // Nama belakang
            'gender',          // Jenis kelamin
            'email',           // Alamat email
            'phone',           // Nomor telepon
            'role',            // Role (admin atau user)
        ];
    }

    // Which fields can be used to sort the results through the query string
    public function getAllowedSorts(): array
    {
        return [
            'profile_picture', // Foto profil
            'name',       // Nama depan
            'lastname',        // Nama belakang
            'gender',          // Jenis kelamin
            'email',           // Alamat email
            'phone',           // Nomor telepon
            'role',            // Role (admin atau user)
        ];
    }

    // Which fields can be used to filter the results through the query string
    public function getAllowedFilters(): array
    {
        return [
            'profile_picture', // Foto profil
            'name',       // Nama depan
            'lastname',        // Nama belakang
            'gender',          // Jenis kelamin
            'email',           // Alamat email
            'phone',           // Nomor telepon
            'role',            // Role (admin atau user)
        ];
    }
}
