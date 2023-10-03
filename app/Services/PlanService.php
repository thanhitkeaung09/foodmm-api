<?php

declare(strict_types=1);

namespace App\Services;

use App\Builders\PlanBuilder;
use App\Dto\PlanData;
use App\Enums\LimitType;
use App\Models\Plan;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class PlanService
{
    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    public function getAll(?int $collectionId = null, ?int $userId = null): LengthAwarePaginator
    {
        return $this->getBaseQuery($collectionId, $userId)
            ->orderBy('reminded_at')
            ->paginate(LimitType::PAGINATE->value)
            ->through(function ($plan) {
                $this->addAverageRating($plan);

                $plan->day = now()->diffInDays($plan->planed_at);

                return $plan;
            });
    }

    public function getUpcomming(?int $collectionId = null, ?int $userId = null): LengthAwarePaginator
    {
        return $this->getBaseQuery($collectionId, $userId)
            ->whereUpcoming()
            ->orderBy('reminded_at')
            ->paginate(LimitType::PAGINATE->value)
            ->through(function ($plan) {
                $this->addAverageRating($plan);

                $plan->day = now()->diffInDays($plan->planed_at);

                return $plan;
            });
    }

    public function getHistory(?int $collectionId = null, ?int $userId = null): LengthAwarePaginator
    {
        return $this->getBaseQuery($collectionId, $userId)
            ->whereHistory()
            ->orderByDesc('planed_at')
            ->paginate(LimitType::PAGINATE->value)
            ->through(function ($plan) {
                $this->addAverageRating($plan);

                $plan->day = now()->diffInDays($plan->planed_at, false);

                return $plan;
            });
    }

    public function getToday(?int $collectionId = null, ?int $userId = null): LengthAwarePaginator
    {
        return $this->getBaseQuery($collectionId, $userId)
            ->whereToday()
            ->orderBy('reminded_at')
            ->paginate(LimitType::PAGINATE->value);
    }

    public function create(PlanData $data): Plan
    {
        if (now()->greaterThanOrEqualTo($data->planedAt)) {
            throw new Exception("A plan can't create with the previous date and time!");
        }

        $plan = $this->userService->getSamePlan($data);

        if (is_null($plan)) {
            $plan = Plan::query()->create($data->toArray());
            $plan->foods()->attach($data->foods);
        } else {
            $plan->foods()->sync($data->foods);
        }

        return $plan;
    }

    public function findPlan(Plan $plan)
    {
        $plan = $plan->load(['restaurant', 'shop', 'foods', 'collection']);

        $this->addAverageRating($plan);

        $plan->day = now()->diffInDays($plan->planed_at, false);

        return $plan;
    }

    public function update(Plan $plan, PlanData $data): bool
    {
        if (now()->greaterThanOrEqualTo($data->planedAt)) {
            throw new Exception('Plan can\'t not update with back datetime!');
        }

        $plan->foods()->sync($data->foods);

        return $plan->update($data->toArray());
    }

    public function delete(Plan $plan): void
    {
        $plan->foods()->detach();

        $plan->delete();
    }

    private function addAverageRating(Plan $plan): void
    {
        if ($plan->restaurant) {
            $plan->restaurant->averageRating = $plan->restaurant->averageRating();
            unset($plan->restaurant->ratings);
        }

        if ($plan->shop) {
            $plan->shop->averageRating = $plan->shop->averageRating();
            unset($plan->shop->ratings);
        }
    }

    private function getBaseQuery(?int $collectionId, ?int $userId): PlanBuilder
    {
        return Plan::query()
            ->with([
                'user', 'restaurant', 'shop', 'foods', 'collection', 'restaurant.ratings',
                'shop.ratings'
            ])
            ->when($userId, function ($q, $userId) {
                $q->where('user_id', $userId);
            })
            ->when($collectionId, function ($q, $collectionId) {
                $q->where('collection_id', $collectionId);
            });
    }
}
