---
description: Continue PHP 8.4 migration from refactor-tasks.md for the next 5 files, without re-running the tool
allowed-tools: Read, Edit, Write, Grep, Glob
---

Continue the PHP 5.3.10 to PHP 8.4.27 migration. Follow CLAUDE.md and `php84-migrate-folder.prompt.md`. Use Grep to find, then Read only the section you need.

## Environment
- PHP 8.4.27 is already installed on the Ubuntu server that hosts this project. Assume it. Do not ask me to confirm the version.
- This session edits code only. Run no shell commands: no refactor tool, no PHP version, binary, or extension checks (`php -v`, `which php`, `php -m`), no `php -l`, `phpcs`, tests, or git.
- Do not execute any PHP file.
- Start at Step 1 immediately. Do not run any preliminary environment or setup check.

## Steps
1. Read `refactor-tasks.md` and pick the next 5 files that still have open items. Skip items already marked `[x]`.
2. Edit code by hand, one file at a time, following the Conversion rules below.
3. After each file, mark its cleared items `[x]` in `refactor-tasks.md` and show a short summary: file path, items cleared, items left, open items.
4. Stop after at most 5 files so I can review. Then list all open items found in this run.

## Conversion rules
- Convert `mysql_*` to PDO prepared statements (guide 3.1 in `php84-migrate-folder.prompt.md`).
- Keep `mssql_*` on `<MSSQL_PDO_HELPER>`. If MSSQL config is exists.
- Do not add backticks to SQL Server queries.
- Keep behaviour, parameter names, session keys, and JSON keys unchanged.
- Never hard-code credentials.
- Never guess project details. Record every unknown as an open item.