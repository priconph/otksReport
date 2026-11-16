---
description: Migrate PHP 5.3.10 code to PHP 8.4 using the refactor tool, then fix remaining items by hand, max 5 files per run
allowed-tools: Read, Edit, Write, Grep, Glob, Bash(tools/php53-to-84-refactor.php run:*)
---

Migrate this project from PHP 5.3.10 to PHP 8.4.27. Follow CLAUDE.md and `php84-migrate-folder.prompt.md`. Use Grep to find, then Read only the section you need.

## Environment
- PHP 8.4.27 is already installed on the Ubuntu server that hosts this project. Assume it. Do not ask me to confirm the version.
- Never run a command that checks the PHP version, binary, or extensions (`php -v`, `php --version`, `which php`, `php -m`).
- Do not run `php -l`, `phpcs`, tests, or any other check. Do not run any git command.
- Do not execute any PHP file. 

## Run rules
- The only allowed shell command is the refactor tool, run once, using PHP 8.4:
  `tools/php53-to-84-refactor.php run . --quiet --no-backticks --exclude=BACKUP --report=report-all.json --tasks=refactor-tasks.md`

## Steps  
1. Run the tool command above once.
2. Edit code by hand to clear the items in `refactor-tasks.md`, one file at a time, following the Conversion rules below.
3. After each file, mark its cleared items `[x]` in `refactor-tasks.md` and show a short summary: file path, items cleared, items left, open items.
4. Stop after at most 5 files so I can review. Then list all open items found in this run.

## Conversion rules
- Convert `mysql_*` to PDO prepared statements (guide 3.1 in `php84-migrate-folder.prompt.md`).
- Keep `mssql_*` on the separate SQL Server PDO helper (`<MSSQL_PDO_HELPER>`). If MSSQL config is exists.
- Do not add backticks to SQL Server queries.
- Keep behaviour, parameter names, session keys, and JSON keys unchanged.
- Never hard-code credentials.
- Never guess project details. Record every unknown as an open item.