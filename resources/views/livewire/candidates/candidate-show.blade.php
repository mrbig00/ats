<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-2">
                <flux:button variant="ghost" icon="arrow-left" :href="route('candidates.index')" wire:navigate>
                    {{ __('common.back') }}
                </flux:button>
                <flux:heading size="xl" level="1">{{ $candidate->person->fullName() }}</flux:heading>
            </div>
            <div class="flex flex-wrap gap-2">
                @can('convertToEmployee', $candidate)
                    @if (!$candidate->person->employee)
                        <flux:button variant="primary" icon="user-plus" wire:click="openConvertModal">
                            {{ __('candidate.convert_to_employee') }}
                        </flux:button>
                    @endif
                @endcan
                <flux:button icon="calendar-days" wire:click="openInterviewModal" variant="outline">
                    {{ __('candidate.schedule_interview') }}
                </flux:button>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <flux:card>
                    <flux:heading size="lg" class="mb-4">{{ __('candidate.details') }}</flux:heading>
                    <dl class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('candidate.email') }}</flux:text>
                            <flux:text>{{ $candidate->person->email ?? '—' }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('candidate.phone') }}</flux:text>
                            <flux:text>{{ $candidate->person->phone ?? '—' }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('candidate.position') }}</flux:text>
                            <flux:text>{{ $candidate->position->title }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('candidate.stage') }}</flux:text>
                            <div class="flex items-center gap-2 mt-1">
                                <flux:select wire:model.live="newStageId" wire:change.debounce.500ms="updateStage" class="min-w-[160px]">
                                    @foreach ($pipelineStages as $stage)
                                        <flux:select.option :value="$stage->id">{{ $stage->label() }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                            </div>
                        </div>
                        <div>
                            <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('candidate.source') }}</flux:text>
                            <flux:text>{{ $candidate->source ?? '—' }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('candidate.applied_at') }}</flux:text>
                            <flux:text>{{ $candidate->applied_at?->isoFormat('L') ?? '—' }}</flux:text>
                        </div>
                    </dl>
                </flux:card>

                <flux:card>
                    <flux:heading size="lg" class="mb-4">{{ __('candidate.notes') }}</flux:heading>
                    <form wire:submit="addNote" class="mb-4">
                        <flux:field>
                            <flux:textarea wire:model="noteContent" :placeholder="__('candidate.add_note_placeholder')" rows="3" />
                            <flux:error name="noteContent" />
                        </flux:field>
                        <flux:button type="submit" size="sm" wire:loading.attr="disabled">{{ __('candidate.add_note') }}</flux:button>
                    </form>
                    <ul class="space-y-3">
                        @foreach ($candidate->notes as $note)
                            <li class="rounded-lg border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-700 dark:bg-zinc-900" wire:key="note-{{ $note->id }}">
                                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">{{ $note->user->name }} · {{ $note->created_at->isoFormat('L LT') }}</flux:text>
                                <flux:text class="mt-1 whitespace-pre-wrap">{{ $note->content }}</flux:text>
                            </li>
                        @endforeach
                        @if ($candidate->notes->isEmpty())
                            <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('candidate.no_notes') }}</flux:text>
                        @endif
                    </ul>
                </flux:card>

                <flux:card>
                    <flux:heading size="lg" class="mb-4">{{ __('candidate.interviews') }}</flux:heading>
                    <ul class="space-y-2">
                        @foreach ($candidate->interviews as $interview)
                            <li class="flex items-center justify-between rounded-lg border border-zinc-200 p-3 dark:border-zinc-700" wire:key="interview-{{ $interview->id }}">
                                <div>
                                    <flux:text class="font-medium">{{ $interview->title }}</flux:text>
                                    <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">{{ $interview->starts_at->isoFormat('L LT') }}</flux:text>
                                </div>
                            </li>
                        @endforeach
                        @if ($candidate->interviews->isEmpty())
                            <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('candidate.no_interviews') }}</flux:text>
                        @endif
                    </ul>
                </flux:card>
            </div>

            <div class="space-y-6">
                <flux:card>
                    <flux:heading size="lg" class="mb-4">{{ __('candidate.documents') }}</flux:heading>
                    <form wire:submit="uploadDocument" class="mb-4 space-y-3">
                        <flux:field>
                            <flux:label>{{ __('candidate.document_name') }}</flux:label>
                            <flux:input wire:model="documentName" type="text" />
                            <flux:error name="documentName" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('candidate.document') }}</flux:label>
                            <input type="file" wire:model="documentFile" class="block w-full text-sm" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png" />
                            <flux:error name="documentFile" />
                        </flux:field>
                        <flux:button type="submit" size="sm" wire:loading.attr="disabled">{{ __('candidate.upload_document') }}</flux:button>
                    </form>
                    <ul class="space-y-2">
                        @foreach ($candidate->documents as $doc)
                            <li class="flex items-center justify-between rounded-lg border border-zinc-200 p-2 dark:border-zinc-700" wire:key="doc-{{ $doc->id }}">
                                <a href="{{ route('candidates.documents.download', [$candidate, $doc]) }}" class="text-sm font-medium hover:underline" target="_blank" rel="noopener">
                                    {{ $doc->name }}
                                </a>
                                <flux:button size="sm" variant="ghost" icon="trash" wire:click="deleteDocument({{ $doc->id }})" wire:confirm="{{ __('candidate.confirm_delete_document') }}"></flux:button>
                            </li>
                        @endforeach
                        @if ($candidate->documents->isEmpty())
                            <flux:text class="text-zinc-500 dark:text-zinc-400 text-sm">{{ __('candidate.no_documents') }}</flux:text>
                        @endif
                    </ul>
                </flux:card>
            </div>
        </div>

        <flux:modal wire:model="showConvertModal" class="max-w-md">
            <flux:heading size="lg">{{ __('candidate.convert_to_employee') }}</flux:heading>
            <p class="mt-2 text-zinc-500 dark:text-zinc-400">{{ __('candidate.convert_confirm') }}</p>
            <form wire:submit="convertToEmployee" class="mt-4 space-y-4">
                <flux:field>
                    <flux:label>{{ __('candidate.entry_date') }}</flux:label>
                    <flux:input type="date" wire:model="convertEntryDate" required />
                    <flux:error name="convertEntryDate" />
                </flux:field>
                <div class="flex gap-2 justify-end">
                    <flux:button type="button" variant="ghost" wire:click="$set('showConvertModal', false)">{{ __('common.cancel') }}</flux:button>
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled">{{ __('candidate.convert_to_employee') }}</flux:button>
                </div>
            </form>
        </flux:modal>

        <flux:modal wire:model="showInterviewModal" class="max-w-md">
            <flux:heading size="lg">{{ __('candidate.schedule_interview') }}</flux:heading>
            <form wire:submit="scheduleInterview" class="mt-4 space-y-4">
                <flux:field>
                    <flux:label>{{ __('meeting.title') }}</flux:label>
                    <flux:input type="text" wire:model="interviewTitle" required />
                    <flux:error name="interviewTitle" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('meeting.starts_at') }}</flux:label>
                    <flux:input type="datetime-local" wire:model="interviewStartsAt" required />
                    <flux:error name="interviewStartsAt" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('meeting.ends_at') }}</flux:label>
                    <flux:input type="datetime-local" wire:model="interviewEndsAt" />
                    <flux:error name="interviewEndsAt" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('meeting.notes') }}</flux:label>
                    <flux:textarea wire:model="interviewNotes" rows="3" />
                    <flux:error name="interviewNotes" />
                </flux:field>
                <div class="flex gap-2 justify-end">
                    <flux:button type="button" variant="ghost" wire:click="$set('showInterviewModal', false)">{{ __('common.cancel') }}</flux:button>
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled">{{ __('candidate.schedule_interview') }}</flux:button>
                </div>
            </form>
        </flux:modal>
</div>
