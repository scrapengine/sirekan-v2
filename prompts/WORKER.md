# WORKER

Design/implement only the requested worker.

Worker requirements:
- independent lifecycle
- configurable enabled/disabled
- configurable interval
- non-overlap lock
- timeout
- retry
- structured logging
- success/failure state
- must not stop unrelated workers
- graceful shutdown

Never hardcode 10/30/60 minute intervals.

Separate scheduler, orchestration, external client, parsing, business logic, and DB sync.
