<?php


namespace Golly\Authority\Models\Traits;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Trait HasAvatar
 * @package Golly\Authority\Models\Traits
 */
trait HasAvatar
{

    /**
     * Update the user's profile photo.
     *
     * @param UploadedFile $photo
     * @return void
     */
    public function updateAvatar(UploadedFile $photo)
    {
        tap($this->avatar, function ($previous) use ($photo) {
            $this->forceFill([
                'avatar' => $photo->storePublicly(
                    'avatars', ['disk' => $this->getAvatarDisk()]
                ),
            ])->save();

            if ($previous) {
                Storage::disk($this->getAvatarDisk())->delete($previous);
            }
        });
    }

    /**
     * Delete the user's profile photo.
     *
     * @return void
     */
    public function deleteAvatar()
    {
        Storage::disk($this->getAvatarDisk())->delete($this->avatar);

        $this->forceFill([
            'avatar' => null,
        ])->save();
    }

    /**
     * Get the URL to the user's avatar.
     *
     * @param $value
     * @return string
     */
    public function getAvatarAttribute($value): string
    {
        return $value
            ? Storage::disk($this->getAvatarDisk())->url($value)
            : $this->getDefaultAvatarUrl();
    }

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     *
     * @return string
     */
    protected function getDefaultAvatarUrl(): string
    {
        return '';
    }

    /**
     * Get the disk that profile photos should be stored on.
     *
     * @return string
     */
    protected function getAvatarDisk(): string
    {
        return isset($_ENV['VAPOR_ARTIFACT_NAME']) ? 's3' : 'public';
    }
}
