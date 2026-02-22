import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import allLocales from '@fullcalendar/core/locales-all';

let calendarInstance = null;
let dashboardCalendarInstance = null;

function initDashboardCalendar() {
    const calendarEl = document.getElementById('dashboard-calendar');
    if (!calendarEl) {
        if (dashboardCalendarInstance) {
            dashboardCalendarInstance.destroy();
            dashboardCalendarInstance = null;
        }
        return;
    }

    if (dashboardCalendarInstance) {
        dashboardCalendarInstance.destroy();
        dashboardCalendarInstance = null;
    }

    const apiUrl = calendarEl.dataset.apiUrl;
    const locale = calendarEl.dataset.locale || 'en';

    dashboardCalendarInstance = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        locales: allLocales,
        locale,
        headerToolbar: {
            start: 'prev,next today',
            center: 'title',
            end: '',
        },
        height: 'auto',
        events: async (fetchInfo, successCallback, failureCallback) => {
            try {
                const params = new URLSearchParams({
                    start: fetchInfo.startStr,
                    end: fetchInfo.endStr,
                });
                const response = await fetch(`${apiUrl}?${params}`, {
                    method: 'GET',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                const data = await response.json();
                const raw = data.data ?? data;
                const events = Array.isArray(raw)
                    ? raw.map((event) => ({
                        id: event.id,
                        title: event.title,
                        start: event.starts_at,
                        end: event.ends_at || null,
                        url: event.url || null,
                        allDay: event.all_day === true,
                        extendedProps: {
                            type: event.type || '',
                        },
                      }))
                    : [];
                successCallback(events);
            } catch (err) {
                console.warn('Dashboard calendar events fetch failed:', err);
                successCallback([]);
            }
        },
        eventDisplay: 'block',
        eventDidMount: (info) => {
            const type = (info.event.extendedProps.type || '').replace(/_/g, '-');
            if (type) info.el.classList.add('fc-event--' + type);
            if (info.event.allDay) info.el.classList.add('fc-event--all-day');
        },
    });

    dashboardCalendarInstance.render();
}

function initMeetingCalendar() {
    const calendarEl = document.getElementById('meeting-calendar');
    if (!calendarEl) {
        if (calendarInstance) {
            calendarInstance.destroy();
            calendarInstance = null;
        }
        return;
    }

    if (calendarInstance) {
        calendarInstance.destroy();
        calendarInstance = null;
    }

    const apiUrl = calendarEl.dataset.apiUrl;
    const meetingShowBaseUrl = calendarEl.dataset.meetingShowBaseUrl;
    const locale = calendarEl.dataset.locale || 'en';
    const createMeetingUrl = calendarEl.dataset.createMeetingUrl || '';

    calendarInstance = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        locales: allLocales,
        locale,
        headerToolbar: {
            start: 'prev,next today',
            center: 'title',
            end: '',
        },
        height: 'auto',
        events: async (fetchInfo, successCallback, failureCallback) => {
            try {
                const params = new URLSearchParams({
                    start: fetchInfo.startStr,
                    end: fetchInfo.endStr,
                });
                const response = await fetch(`${apiUrl}?${params}`, {
                    method: 'GET',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                const data = await response.json();
                const raw = data.data ?? data;
                const events = Array.isArray(raw)
                    ? raw.map((event) => ({
                        id: event.id,
                        title: event.title,
                        start: event.starts_at,
                        end: event.ends_at || null,
                        url: event.url || `${meetingShowBaseUrl}/${event.id}`,
                        allDay: event.all_day === true,
                        extendedProps: {
                            type: event.type || '',
                        },
                    }))
                    : [];
                successCallback(events);
            } catch (err) {
                failureCallback(err);
            }
        },
        eventDisplay: 'block',
        dateClick: (info) => {
            if (createMeetingUrl) {
                const date = info.dateStr;
                const url = new URL(createMeetingUrl, window.location.origin);
                url.searchParams.set('starts_at', `${date}T10:00`);
                url.searchParams.set('ends_at', `${date}T11:00`);
                window.location.href = url.toString();
            }
        },
        eventDidMount: (info) => {
            const type = (info.event.extendedProps.type || '').replace(/_/g, '-');
            if (type === 'task') {
                info.el.classList.add('fc-event--task');
            } else {
                const isInterview = type === 'interview';
                info.el.classList.add(
                    isInterview ? 'fc-event--interview' : 'fc-event--internal'
                );
            }
            if (info.event.allDay) info.el.classList.add('fc-event--all-day');
        },
    });

    calendarInstance.render();
}

function initAllCalendars() {
    initMeetingCalendar();
    initDashboardCalendar();
}

document.addEventListener('livewire:navigated', () => {
    setTimeout(initAllCalendars, 150);
});
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(initAllCalendars, 50);
});

window.initDashboardCalendar = initDashboardCalendar;
