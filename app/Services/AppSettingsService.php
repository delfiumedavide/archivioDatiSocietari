<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class AppSettingsService
{
    private ?AppSetting $cached = null;

    public function get(): AppSetting
    {
        if ($this->cached) {
            return $this->cached;
        }

        $this->cached = Cache::remember('app_settings', 3600, function () {
            return AppSetting::instance();
        });

        return $this->cached;
    }

    public function update(array $data): AppSetting
    {
        $settings = AppSetting::instance();
        $settings->update($data);

        $this->clearCache();

        return $settings;
    }

    public function updateLogo(UploadedFile $file): string
    {
        $settings = AppSetting::instance();

        if ($settings->logo_path) {
            Storage::disk('public')->delete($settings->logo_path);
        }

        $path = $file->store('branding', 'public');

        $settings->update(['logo_path' => $path]);
        $this->clearCache();

        return $path;
    }

    public function updateFavicon(UploadedFile $file): string
    {
        $settings = AppSetting::instance();

        if ($settings->favicon_path) {
            Storage::disk('public')->delete($settings->favicon_path);
        }

        $path = $file->store('branding', 'public');

        $settings->update(['favicon_path' => $path]);
        $this->clearCache();

        return $path;
    }

    public function removeLogo(): void
    {
        $settings = AppSetting::instance();

        if ($settings->logo_path) {
            Storage::disk('public')->delete($settings->logo_path);
            $settings->update(['logo_path' => null]);
            $this->clearCache();
        }
    }

    private function clearCache(): void
    {
        Cache::forget('app_settings');
        $this->cached = null;
    }
}
