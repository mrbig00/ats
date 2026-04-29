# Job metrics (reporting definitions)

This document defines **stable reporting semantics** for the Jobs KPIs shown on `jobs.index` and `jobs.show`.

## Funnel / pipeline KPIs

KPIs are counted from the **current** candidate pipeline stage (`candidates.pipeline_stage_id` → `pipeline_stages.key`).

- **Applied**: `pipeline_stages.key = applied`
- **Interview**: `pipeline_stages.key IN (screening, interview)`
- **Offer**: `pipeline_stages.key = offer`
- **Hired**: `pipeline_stages.key = hired`

Notes:

- Candidates in `rejected` are not included in the funnel KPIs.
- Stage history is stored in the activity log and is not used for these counts (counts are based on the current stage only).

## Open days

Two values are shown:

### Open for (days)

- **Start date**: `opens_at` if set, otherwise `created_at`
- **End date**:
  - If the posting is active (not expired): **today**
  - If the posting is expired/closed: `closes_at` if set, otherwise `updated_at`
- Output: non-negative integer day difference (same-day results in `0`).

### Closes in (days)

- If `closes_at` is null → show “—”.
- Otherwise: `closes_at - today` in days, clamped to a minimum of `0` for display.

## Urgency

`positions.urgency` is an optional enum used for operational signalling:

- `urgent`
- `medium`
- `good`

If unset, UI treats it as “not specified”.

