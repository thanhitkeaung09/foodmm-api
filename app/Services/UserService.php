<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\PlanData;
use App\Dto\UpdateUserData;
use App\Dto\UserData;
use App\Enums\Language;
use App\Enums\LimitType;
use App\Models\Food;
use App\Models\Restaurant;
use App\Models\Review;
use App\Models\Shop;
use App\Models\User;
use App\Services\FileStorage\FileStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UserService
{
    public function __construct(
        private FileStorageService $fileStorageService,
    ) {
    }

    public function getPaginate(array $columns = ['*']): LengthAwarePaginator
    {
        return User::query()->with('appRating')->paginate(
            columns: $columns,
            perPage: LimitType::PAGINATE->value
        );
    }

    public function firstById($id): ?User
    {
        return User::query()->where('id', $id)->first();
    }

    public function findBySocialType(string $type, string $id): ?User
    {
        return User::query()
            ->where('social_type', $type)
            ->where('social_id', $id)
            ->first();
    }

    public function findEmailOrPhone(string $email, string $phone): ?User
    {
        return User::query()
            ->where('email', $email)
            ->orWhere('phone', $phone)
            ->first();
    }

    public function create(UserData $userData): User
    {
        $path  = $this->fileStorageService->put(
            \config('filesystems.folders.profiles'),
            $userData->profile,
        );

        return User::create([
            ...$userData->toArray(),
            'profile_image' => $path,
        ]);
    }

    public function update(User $user, UpdateUserData $userData): bool
    {
        $data = $userData->toArray();

        if ($userData->profile) {
            $this->fileStorageService->delete($user->getRawOriginal('profile_image'));

            $data['profile_image'] = $this->fileStorageService
                ->upload(\config('filesystems.folders.profiles'), $userData->profile);
        }

        return $user->update($data);
    }

    public function delete(User $user): bool
    {
        if ($user->social_type !== 'email') {
            $user->update(['social_id' => $user->social_id . '_' . now()->timestamp]);
        }

        $this->deleteRelations($user);

        return $user->delete();
    }

    public function getProfile()
    {
        $profile = auth()->user()->loadCount(['preferred', 'reviews'])->load('appRating');

        $reviewCount = 0;

        $profile->reviews->map(function ($review) use (&$reviewCount) {
            $reviewable = $review->reviewable;

            if ($reviewable::class === Food::class) {
                if ($reviewable->shops->count() > 0) {
                    if ($reviewable->shops->first()->location->township->city_id === (int)request('city_id')) {
                        $reviewCount++;
                    }
                } elseif ($reviewable->restaurants->count() > 0) {
                    if ($reviewable->restaurants->first()->location->township->city_id === (int)request('city_id')) {
                        $reviewCount++;
                    }
                }
            } else {
                if ($reviewable->location->township->city_id === (int) request('city_id')) {
                    $reviewCount++;
                }
            }
        });

        $profile->reviews_count = $reviewCount;

        unset($profile->reviews);

        return $profile;
    }

    public function updateName(User $user, string $newName): bool
    {
        return $user->update(['name' => $newName]);
    }

    public function updateImage(User $user, UploadedFile $newImage): User
    {
        $this->fileStorageService->delete($user->getRawOriginal('profile_image'));

        $path = $this->fileStorageService->upload(\config('filesystems.folders.profiles'), $newImage);

        $user->update(['profile_image' => $path]);
        return $user;
    }

    public function updateLanguage(User $user, Language $language): bool
    {
        return $user->update(['language' => $language->value]);
    }

    public function updateDeviceToken(User $user, string $deviceToken): bool
    {
        return $user->update(['device_token' => $deviceToken]);
    }

    public function getReviews(User $user): User
    {
        $reviews = $user->reviews->load(['reviewable', 'reviewable.ratings']);

        $reviews = $this->classify($reviews);

        unset($user->reviews);

        $user->reviews = $reviews;

        return $user;
    }

    public function getSamePlan(PlanData $data)
    {
        return auth()->user()->load(['plans' => function ($q) use ($data) {
            $q->whereRaw('reminded_at >= NOW()')
                ->whereRaw('planed_at = "' . $data->planedAt->toDateTimeString() . '"')
                ->where(function ($q) use ($data) {
                    $q->when($data->shopId, fn ($q, $id) => $q->where('shop_id', $id))
                        ->when($data->restaurantId, fn ($q, $id) => $q->where('shop_id', $id));
                });
        }])->plans->first();
    }

    public function getAllCount(): int
    {
        return User::query()->count();
    }

    private function deleteRelations(User $user): void
    {
        $user->appRating()->delete();
        $user->reviews()->delete();
        $user->ratings()->delete();
        $user->preferred()->delete();
        $user->collections()->delete();
        $user->plans()->delete();
    }

    private function classify(Collection $reviews): Collection
    {
        $types = [
            Food::class => 'food',
            Restaurant::class => 'restaurant',
            Shop::class => 'shop',
        ];

        $reviews->each(function ($r) use ($types) {
            $this->transformType($r, $types[$r->reviewable_type]);
        });

        return
            $reviews->filter(function ($review) {
                $reviewable = $review->reviewable;
                if ($reviewable::class === Food::class) {
                    if ($reviewable->shops->count() > 0) {
                        $location = $reviewable->shops->first()->location->township->city_id;
                        unset($reviewable->shops->first()->location);
                        unset($reviewable->description);
                        unset($reviewable->category_id);
                        unset($reviewable->location_id);
                        unset($reviewable->opening_hours);
                        unset($reviewable->phones);
                        unset($reviewable->food_type_id);
                        unset($reviewable->ingredients);
                        unset($reviewable->vitamins);
                        unset($reviewable->calories);
                        unset($reviewable->is_popular);
                        unset($reviewable->shops);
                        unset($reviewable->restaurants);
                        return $location === (int)request('city_id');
                    } else {
                        $location = $reviewable->restaurants->first()->location->township->city_id;
                        unset($reviewable->restaurants->first()->location);
                        unset($reviewable->description);
                        unset($reviewable->category_id);
                        unset($reviewable->location_id);
                        unset($reviewable->opening_hours);
                        unset($reviewable->phones);
                        return $location === (int)request('city_id');
                    }
                } else {
                    $location = $reviewable?->location?->township?->city_id;
                    unset($reviewable->location);
                    unset($reviewable->description);
                    unset($reviewable->category_id);
                    unset($reviewable->location_id);
                    unset($reviewable->opening_hours);
                    unset($reviewable->phones);
                    return $location === (int) request('city_id');
                }
            })->values();
    }

    private function transformType(Review $review, string $type): void
    {
        $review->reviewable->average_rate = $review->reviewable->ratings->first()?->average_rate ?? "0.0";
        $review->reviewable->type = $type;
        unset($review->reviewable->ratings);
    }
}
