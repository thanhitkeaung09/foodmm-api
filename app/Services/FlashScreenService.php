<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\FlashScreenData;
use App\Models\FlashScreen;
use App\Services\FileStorage\FileStorageService;

class FlashScreenService
{
    public function __construct(
        private readonly FileStorageService $fileStorageService
    ) {
    }

    public function getPaginate(array $column = ['*'])
    {
        return FlashScreen::query()->paginate(columns: $column);
    }

    public function create(FlashScreenData $data): FlashScreen
    {
        $path = $this->fileStorageService->upload(
            \config('filesystems.folders.flash_screens'),
            $data->image
        );

        return FlashScreen::query()->create([
            ...$data->toArray(),
            'flash_screen_image' => $path,
        ]);
    }

    public function update(FlashScreen $flashScreen, FlashScreenData $data): bool
    {
        $attributes = $data->toArray();

        if ($data->image) {
            $this->fileStorageService->delete($flashScreen->flash_screen_image);

            $attributes['flash_screen_image'] = $this->fileStorageService->upload(
                \config('filesystems.folders.flash_screens'),
                $data->image
            );
        }

        return $flashScreen->update($attributes);
    }

    public function delete(FlashScreen $flashScreen): bool
    {
        $this->fileStorageService->delete($flashScreen->flash_screen_image);

        return $flashScreen->delete();
    }
}
