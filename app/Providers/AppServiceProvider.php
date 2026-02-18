<?php

namespace App\Providers;

use App\Events\CandidateCreated;
use App\Events\CandidateHired;
use App\Events\CandidateStageChanged;
use App\Events\EmployeeTerminated;
use App\Events\InterviewScheduled;
use App\Events\MeetingScheduled;
use App\Listeners\LogActivityEventListeners;
use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::prependNamespace(
            'laravel-exceptions-renderer',
            resource_path('views/vendor/laravel-exceptions-renderer')
        );
        $this->configureDefaults();
        $this->configureApiRateLimiting();
        $this->registerActivityEventListeners();
    }

    private function registerActivityEventListeners(): void
    {
        $listeners = app(LogActivityEventListeners::class);
        Event::listen(CandidateCreated::class, [$listeners, 'handleCandidateCreated']);
        Event::listen(CandidateStageChanged::class, [$listeners, 'handleCandidateStageChanged']);
        Event::listen(CandidateHired::class, [$listeners, 'handleCandidateHired']);
        Event::listen(EmployeeTerminated::class, [$listeners, 'handleEmployeeTerminated']);
        Event::listen(MeetingScheduled::class, [$listeners, 'handleMeetingScheduled']);
        Event::listen(InterviewScheduled::class, [$listeners, 'handleInterviewScheduled']);
    }

    /**
     * Configure the API rate limiter (used by throttle:api middleware).
     */
    private function configureApiRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }
}
