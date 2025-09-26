<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'username',
        'nip',
        'phone',
        'position',
        'status',
        'department_id',
        'signature_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the department that owns the user.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the letters created by the user.
     */
    public function createdLetters()
    {
        return $this->hasMany(Letter::class, 'created_by');
    }

    /**
     * Get the letter dispositions from the user.
     */
    public function sentDispositions()
    {
        return $this->hasMany(LetterDisposition::class, 'from_user_id');
    }

    /**
     * Get the letter dispositions to the user.
     */
    public function receivedDispositions()
    {
        return $this->hasMany(LetterDisposition::class, 'to_user_id');
    }

    /**
     * Get the letter attachments uploaded by the user.
     */
    public function uploadedAttachments()
    {
        return $this->hasMany(LetterAttachment::class, 'uploaded_by');
    }

    /**
     * Get the letter signatures by the user.
     */
    public function letterSignatures()
    {
        return $this->hasMany(LetterSignature::class);
    }

    /**
     * Get the letter agendas created by the user.
     */
    public function createdAgendas()
    {
        return $this->hasMany(LetterAgenda::class, 'created_by');
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is rektorat.
     */
    public function isRektorat(): bool
    {
        return $this->role === 'rektorat';
    }

    /**
     * Check if user is unit kerja.
     */
    public function isUnitKerja(): bool
    {
        return $this->role === 'unit_kerja';
    }
}
