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
                <flux:button icon="clock" wire:click="openChangeHistoryModal" variant="outline">
                    {{ __('candidate.show_stage_history') }}
                </flux:button>
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
                            <div class="flex flex-col gap-2 mt-1">
                                <flux:select wire:model.live="newStageId" wire:change.debounce.500ms="updateStage" class="min-w-[160px]">
                                    @foreach ($pipelineStages as $stage)
                                        <flux:select.option :value="$stage->id">{{ $stage->label() }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                @if ($stageSetByLine)
                                    <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">{{ $stageSetByLine }}</flux:text>
                                @endif
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
                        @cannot('update', $candidate)
                            <div>
                                <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('candidate.nationality') }}</flux:text>
                                <flux:text>{{ $candidate->nationality ?? '—' }}</flux:text>
                            </div>
                            <div>
                                <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('candidate.driving_license_category') }}</flux:text>
                                <flux:text>{{ $candidate->driving_license_category ?? '—' }}</flux:text>
                            </div>
                            <div>
                                <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('candidate.has_own_car') }}</flux:text>
                                <flux:text>@if($candidate->has_own_car === null){{ '—' }}@elseif($candidate->has_own_car){{ __('common.yes') }}@else{{ __('common.no') }}@endif</flux:text>
                            </div>
                            <div>
                                <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('candidate.german_level') }}</flux:text>
                                <flux:text>{{ $candidate->german_level?->label() ?? '—' }}</flux:text>
                            </div>
                            <div>
                                <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('candidate.available_from') }}</flux:text>
                                <flux:text>{{ $candidate->available_from?->isoFormat('L') ?? '—' }}</flux:text>
                            </div>
                            <div>
                                <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('candidate.housing_needed') }}</flux:text>
                                <flux:text>@if($candidate->housing_needed === null){{ '—' }}@elseif($candidate->housing_needed){{ __('common.yes') }}@else{{ __('common.no') }}@endif</flux:text>
                            </div>
                        @endcannot
                    </dl>
                </flux:card>

                @can('update', $candidate)
                    <flux:card>
                        <flux:heading size="lg" class="mb-4">{{ __('candidate.profile_section') }}</flux:heading>
                        <form wire:submit="saveProfile" class="space-y-4">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <flux:field>
                                    <flux:label>{{ __('candidate.nationality') }}</flux:label>
                                    <flux:input wire:model="nationality" type="text" />
                                    <flux:error name="nationality" />
                                </flux:field>
                                <flux:field>
                                    <flux:label>{{ __('candidate.driving_license_category') }}</flux:label>
                                    <flux:input wire:model="driving_license_category" type="text" />
                                    <flux:error name="driving_license_category" />
                                </flux:field>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <flux:field>
                                    <flux:label>{{ __('candidate.has_own_car') }}</flux:label>
                                    <flux:select wire:model="has_own_car">
                                        <flux:select.option value="">{{ __('common.not_specified') }}</flux:select.option>
                                        <flux:select.option value="1">{{ __('common.yes') }}</flux:select.option>
                                        <flux:select.option value="0">{{ __('common.no') }}</flux:select.option>
                                    </flux:select>
                                    <flux:error name="has_own_car" />
                                </flux:field>
                                <flux:field>
                                    <flux:label>{{ __('candidate.german_level') }}</flux:label>
                                    <flux:select wire:model="german_level">
                                        <flux:select.option value="">{{ __('common.not_specified') }}</flux:select.option>
                                        @foreach ($germanLevels as $level)
                                            <flux:select.option :value="$level->value">{{ $level->label() }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="german_level" />
                                </flux:field>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <flux:field>
                                    <flux:label>{{ __('candidate.available_from') }}</flux:label>
                                    <flux:input wire:model="available_from" type="date" />
                                    <flux:error name="available_from" />
                                </flux:field>
                                <flux:field>
                                    <flux:label>{{ __('candidate.housing_needed') }}</flux:label>
                                    <flux:select wire:model="housing_needed">
                                        <flux:select.option value="">{{ __('common.not_specified') }}</flux:select.option>
                                        <flux:select.option value="1">{{ __('common.yes') }}</flux:select.option>
                                        <flux:select.option value="0">{{ __('common.no') }}</flux:select.option>
                                    </flux:select>
                                    <flux:error name="housing_needed" />
                                </flux:field>
                            </div>
                            <flux:button type="submit" size="sm" variant="primary" wire:loading.attr="disabled">{{ __('candidate.save_profile') }}</flux:button>
                        </form>
                    </flux:card>
                @endcan

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
                                <flux:link :href="route('meetings.show', $interview)" wire:navigate class="flex-1">
                                    <flux:text class="font-medium">{{ $interview->title }}</flux:text>
                                    <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">{{ $interview->starts_at->isoFormat('L LT') }}</flux:text>
                                </flux:link>
                                <flux:button size="sm" icon="eye" :href="route('meetings.show', $interview)" wire:navigate variant="ghost" />
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

        <flux:modal wire:model="showChangeHistoryModal" class="max-w-lg">
            <flux:heading size="lg">{{ __('candidate.change_history') }}</flux:heading>
            <div class="mt-4 max-h-[min(70vh,28rem)] overflow-y-auto">
                <ul class="space-y-3 pr-1">
                    @foreach ($changeHistoryRows as $row)
                        <li class="rounded-lg border border-zinc-200 bg-zinc-50 p-3 text-sm dark:border-zinc-700 dark:bg-zinc-900" wire:key="modal-change-hist-{{ $row['id'] }}">
                            <flux:text class="text-zinc-500 dark:text-zinc-400">{{ $row['happened_at']->isoFormat('L LT') }} · {{ $row['actor'] }}</flux:text>
                            <flux:text class="mt-1 font-medium">{{ $row['summary'] }}</flux:text>
                            @if (count($row['lines']) > 0)
                                <ul class="mt-2 space-y-1.5 border-t border-zinc-200 pt-2 dark:border-zinc-700">
                                    @foreach ($row['lines'] as $lineIdx => $line)
                                        <li wire:key="modal-ch-line-{{ $row['id'] }}-{{ $lineIdx }}">
                                            <flux:text class="text-zinc-600 dark:text-zinc-300"><span class="font-medium text-zinc-800 dark:text-zinc-100">{{ $line['label'] }}:</span> {{ $line['from'] }} → {{ $line['to'] }}</flux:text>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                    @if (count($changeHistoryRows) === 0)
                        <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('candidate.no_change_history') }}</flux:text>
                    @endif
                </ul>
            </div>
            <div class="mt-4 flex justify-end border-t border-zinc-200 pt-4 dark:border-zinc-700">
                <flux:button type="button" variant="ghost" wire:click="$set('showChangeHistoryModal', false)">{{ __('common.close') }}</flux:button>
            </div>
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
