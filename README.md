# AI-Assisted Client Intake and Scope Review

A self-initiated portfolio project for exploring how small software teams can turn an unclear client request into a reviewable, versioned, and human-approved project scope.

> **Project status:** Product and architecture documentation phase. The repository currently contains the PRD and implementation plan. Laravel runtime, database migrations, queue workers, browser flows, and external AI provider integration have not been verified in this workspace.

## Why this project exists

Early project requests often arrive as short messages such as:

> “We need a catalog, checkout, customer accounts, and reports in two weeks.”

That request may be a useful starting point, but it does not yet define users, payment rules, data sources, reporting details, assumptions, risks, or what “done” means.

This project explores a safer workflow:

1. Capture the original request without silently changing it.
2. Identify missing information, assumptions, risks, and follow-up questions.
3. Use AI only to produce a structured draft for review.
4. Let a human reviewer edit and create a scope version.
5. Keep every important version instead of overwriting history.
6. Require an authorized human decision before a scope becomes approved.
7. Record important actions in an append-only audit trail.

The application is designed to support human judgment, not replace it.

## What the application is designed to do

- Accept an initial text-based client request.
- Store the original request as an immutable source record.
- Create a structured scope review draft.
- Highlight unclear requirements and follow-up questions.
- Record risks, assumptions, and possible out-of-scope items.
- Support a manual workflow when AI is unavailable or not permitted.
- Create immutable scope versions with parent-child history.
- Compare changes between scope versions.
- Support internal approval and rejection with a required note.
- Protect reviewer and approval endpoints with authentication and authorization.
- Record important actions with actor, subject, status, and correlation metadata.
- Handle duplicate requests, retryable failures, validation failures, and terminal job failures.
- Generate a project brief only from an approved scope version.

## Core workflow

```text
Client request
    -> immutable intake submission
    -> manual review or optional AI analysis
    -> structured review draft
    -> clarification, risk, and assumption review
    -> scope version
    -> human review
    -> approved or rejected
    -> project brief from the approved snapshot
    -> audit timeline
```

AI output remains a draft until a human reviews and selects the information that belongs in a scope version.

## Architecture direction

The planned MVP uses a Laravel monolith with a relational database and a server-side AI adapter.

```text
Browser
  -> Laravel routes and session authentication
  -> Form Requests and policies
  -> domain actions and services
  -> PostgreSQL
  -> database queue for asynchronous analysis
  -> AI provider adapter from the server only
  -> audit events and version history
```

### Planned baseline

| Layer | Planned choice | Reason |
| --- | --- | --- |
| Backend | Laravel 13 | Clear conventions for routing, validation, migrations, policies, and queues |
| UI | Blade and Alpine.js | Keeps the first implementation in one application and is approachable for a small MVP |
| Database | PostgreSQL | Fits relational workflow data, constraints, JSONB snapshots, and transactions |
| Queue | Laravel database queue | Avoids adding Redis before a measured need exists |
| AI boundary | `ScopeReviewGenerator` interface | Keeps fake and real providers replaceable and testable |
| Testing | PHPUnit or the project’s selected Laravel test runner | Covers domain rules, authorization, failure paths, and integration boundaries |

These are planned choices, not evidence that every package combination has already been installed or verified.

## Design principles

### Human approval stays explicit

AI can summarize and identify possible gaps. It cannot approve scope, make a legal commitment, or silently change the source request.

### Source data and generated data stay separate

The original intake request, AI draft, reviewer-edited scope, approved version, and project brief have different meanings. The system should not overwrite one with another.

### Version history is part of the product

An approved scope is an immutable snapshot. Later changes create a new version with a traceable parent instead of editing the approved record in place.

### Manual mode must continue to work

If the provider times out, reaches a rate limit, returns invalid output, or is not approved for a workspace, a reviewer must still be able to create a scope manually.

### Tenant and role boundaries are enforced on the server

The interface may hide actions, but policies and endpoint checks decide whether a user can access a submission or approve a version.

### Audit metadata should be useful but small

Audit events record what happened, who acted, which subject was affected, and how the event can be correlated with a request. They do not store raw intake text, prompts, provider responses, cookies, or API keys by default.

## Scope boundaries

### Included in the MVP direction

- Text-based intake.
- Structured review drafts.
- Clarification questions.
- Risks and assumptions.
- Manual scope editing.
- Immutable scope versions.
- Internal approval and rejection.
- Audit events.
- Idempotency and queue failure handling.
- Synthetic data for demonstrations.

### Deliberately outside the first MVP

- Client portal and external signature.
- Automatic proposal or email delivery.
- PDF, DOCX, spreadsheet, image, or OCR parsing.
- Pricing or delivery-date estimation.
- CRM, Gmail, WhatsApp, Slack, Jira, or payment integrations.
- Real-time collaboration.
- Billing.
- Semantic search and vector storage.
- Compliance or security certification claims.

Keeping these boundaries visible makes the project easier to evaluate and reduces the temptation to build features before validating the workflow.

## Repository status

The current repository contains:

```text
ai-client-intake/
└── PRD.md    Product requirements and implementation direction
```

The PRD documents:

- product goals and non-goals;
- user roles and access boundaries;
- workflow states and transitions;
- data entities and relationships;
- AI output contract;
- planned endpoint structure;
- security and privacy boundaries;
- testing strategy;
- implementation order;
- open decisions before real client data is accepted.

The implementation will be added as a separate, verifiable step. This README does not present planned code as completed software.

## Planned implementation sequence

1. Verify PHP, Composer, database, and frontend tooling.
2. Bootstrap the Laravel application and local health check.
3. Add authentication, session handling, and workspace access rules.
4. Build the intake vertical slice from form to database and back to the UI.
5. Add the manual scope workflow before enabling AI.
6. Add versioning, comparison, approval, rejection, and audit events.
7. Add idempotency and queue failure handling.
8. Run the end-to-end demo using synthetic data.
9. Evaluate a real provider through an isolated server-side adapter.
10. Review privacy, retention, backup, restore, and operational boundaries.

Each step should produce a testable slice rather than a collection of disconnected files.

## Evidence and current limitations

This project is a self-initiated prototype direction using synthetic data. It is not presented as a deployed client system.

At the current stage:

- no real client data is used;
- no customer testimonial is claimed;
- no efficiency, revenue, accuracy, uptime, or ROI statistic is claimed;
- no provider retention policy is assumed;
- no production security or compliance certification is claimed;
- runtime and browser verification are still pending in this workspace;
- owner-only approval remains an explicit model and policy decision to verify when workspace ownership is implemented.

These limitations are part of the project documentation because a portfolio case study should distinguish a design decision from a measured result.

## How to evaluate the project

A useful review should ask:

- Can a reviewer understand the difference between an intake request, an AI draft, a scope version, and an approved brief?
- Does the manual workflow remain useful when AI is unavailable?
- Can the system explain who approved a particular scope snapshot?
- Does a duplicate request create duplicate work?
- Can a user from another workspace access the same record ID?
- Does a rejected version remain available as history?
- Can the system recover from provider timeout or invalid output?
- Does the audit trail avoid copying sensitive content?

The answers should come from tests and runtime evidence as implementation progresses, not from assumptions in the README.

## For potential clients

This project demonstrates how I approach workflow-heavy software:

- clarify the business process before choosing infrastructure;
- separate source data from generated suggestions;
- keep human decisions visible and accountable;
- design failure paths before relying on external services;
- protect access at the server boundary;
- keep architecture proportional to the evidence;
- communicate what is implemented, what is measured, and what remains open.

If you are evaluating a similar intake, review, approval, or AI-assisted workflow, this repository shows the kind of product and engineering questions I would make explicit before implementation.

## License and usage

No license has been selected yet. Treat the repository as a portfolio and learning project until a license is added.

Synthetic examples in the documentation are not client records and must not be represented as real customer data.

