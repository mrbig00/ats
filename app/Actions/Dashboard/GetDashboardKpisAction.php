<?php

namespace App\Actions\Dashboard;

use App\Data\Dashboard\DashboardKpisData;
use App\Models\ActivityEvent;
use App\Repositories\ActivityEventRepository;
use App\Repositories\CalendarEventRepository;
use App\Repositories\CandidateRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\PositionRepository;
use App\Repositories\RoomRepository;
use Carbon\CarbonImmutable;

class GetDashboardKpisAction
{
    public function __construct(
        private ActivityEventRepository $activityEventRepository,
        private CandidateRepository $candidateRepository,
        private CalendarEventRepository $calendarEventRepository,
        private EmployeeRepository $employeeRepository,
        private RoomRepository $roomRepository,
        private PositionRepository $positionRepository,
    ) {}

    public function handle(?int $newApplicantsDays = 30): DashboardKpisData
    {
        $today = CarbonImmutable::today();
        $weekStart = $today->startOfWeek();
        $weekEnd = $today->endOfWeek();
        $monthStart = $today->startOfMonth()->startOfDay();
        $monthEnd = $today->endOfMonth()->endOfDay();
        $fromApplicants = $today->subDays($newApplicantsDays)->startOfDay();
        $toApplicants = $today->endOfDay();

        $activeCandidatesCount = $this->candidateRepository->countActive();
        $interviewsThisWeekCount = $this->calendarEventRepository->countInterviewsInPeriod($weekStart, $weekEnd);
        $hiredThisMonthCount = $this->activityEventRepository->countByTypeInPeriod(
            ActivityEvent::TYPE_EMPLOYEE_HIRED,
            $monthStart,
            $monthEnd
        );
        $activeEmployeesCount = $this->employeeRepository->countActive();
        $freeRoomsCount = $this->roomRepository->countFreeRooms();

        $newApplicantsCount = $this->activityEventRepository->countByTypeInPeriod(
            ActivityEvent::TYPE_CANDIDATE_CREATED,
            $fromApplicants,
            $toApplicants
        );
        $upcomingDeparturesList = $this->employeeRepository->getUpcomingDepartures(30);
        $openPositionsCount = $this->positionRepository->countOpen();

        return new DashboardKpisData(
            activeCandidatesCount: $activeCandidatesCount,
            interviewsThisWeekCount: $interviewsThisWeekCount,
            hiredThisMonthCount: $hiredThisMonthCount,
            activeEmployeesCount: $activeEmployeesCount,
            freeRoomsCount: $freeRoomsCount,
            newApplicantsCount: $newApplicantsCount,
            upcomingDeparturesCount: $upcomingDeparturesList->count(),
            openPositionsCount: $openPositionsCount,
            upcomingDeparturesList: $upcomingDeparturesList,
        );
    }
}
