import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import allLocales from '@fullcalendar/core/locales-all';

let calendarInstance = null;

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
                const events = (data.data ?? data).map((event) => ({
                    id: event.id,
                    title: event.title,
                    start: event.starts_at,
                    end: event.ends_at || null,
                    url: `${meetingShowBaseUrl}/${event.id}`,
                    extendedProps: {
                        type: event.type,
                    },
                }));
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
            const type = info.event.extendedProps.type;
            const isInterview = type === 'interview';
            info.el.classList.add(
                isInterview ? 'fc-event--interview' : 'fc-event--internal'
            );
        },
    });

    calendarInstance.render();
}

document.addEventListener('livewire:navigated', initMeetingCalendar);
document.addEventListener('DOMContentLoaded', initMeetingCalendar);
