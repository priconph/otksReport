---
name: php84-migrate
description: Migrate one PHP entry file (e.g. index.php) and every file it pulls in through require_once / require / include / include_once, recursively, from PHP 5.3.10 to PHP 8.4.27, following the project's CLAUDE.md migration rules. Use whenever the user runs /php84-migrate, names an entry file like @index.php or @page_header.php to migrate, or asks to upgrade, refactor or fix a PHP page "and its includes" for PHP 8 / 8.4 — including mysql_* to PDO conversion, removed functions (ereg, each, create_function, split), or making legacy PHP 5 pages run on PHP 8, even if they only name one file.
---

# /php84-migrate — migrate an entry file and its include tree

The user gives an entry file (`/php84-migrate @index.php`). The job is to
migrate that file first, then every file it reaches through `require_once`,
`require`, `include` and `include_once`, at any depth (e.g. `index.php` →
`page_header.php` → `includes.php`, `get_employee_username.php` → …).

The project's `CLAUDE.md` is the authority. Everything below works inside its
rules; where this skill and `CLAUDE.md` disagree, `CLAUDE.md` wins and the
conflict goes in the open items.

## Ground rules that shape this workflow

These come from `CLAUDE.md`; they are repeated because they change the steps.

- **One folder per session.** The session folder is the entry file's folder
  unless the user names another. Files the include tree reaches *outside* that
  folder are read for context but never edited; they are listed as open items
  for their own folder's session. This applies even though the user asked for
  "all connected files" — say so plainly in the plan instead of silently
  skipping them.
- **Dry-run first, `--write` only after the user approves.** Always `--quiet`
  and `--no-backticks`. Never edit `tools/php53-to-84-refactor.php`.
- **Never guess.** Unknown config names, password formats, timezone, binary
  path, unresolvable include paths: list them as open items.
- **Keep behaviour.** Request param names, session keys, JSON keys and output
  shape stay identical.
- Never hard-code credentials; never echo SQL or exception text; never commit
  `*-tasks.md`, `report.json`, `.refactor-backup/`.

## Step 0 — Load decisions and known open items

Read two sections of `CLAUDE.md` before anything else:

- **Decisions**: anything recorded there is settled. Apply it; do not list it
  as an open item again. Only a decision still marked TBD stays open.
- **Open items (project-wide)**: the user keeps known items there. Don't
  repeat them one by one in the report. Mention them in one line ("N
  project-wide items still open, see CLAUDE.md"), unless this run found new
  evidence about one, such as the old php.ini value or a sample password hash.
  In that case, report the evidence and suggest the Decisions wording.

Never edit `CLAUDE.md` yourself. Suggest the wording, and the user adds it.

## Step 1 — Check the PHP binary

The tool must run under PHP 8.4.27 and the binary path is still an open item.
Run `php -v`. If it is not 8.4.27, look for another binary (`which -a php`,
`ls /usr/bin/php8*`, `ls /usr/local/bin/php*`). If none reports 8.4.27, stop and
ask the user for the path. Do not run the tool or `php -l` with any other version.

## Step 2 — Map the include tree (read-only)

```bash
python3 <skill-dir>/scripts/include_graph.py <entry.php> --folder <session-folder>
```

It prints the edit order (entry first, then breadth-first), each include with
its line number, files marked `[OUTSIDE FOLDER]`, and anything it could not
resolve (`DYNAMIC` paths built from variables, `MISSING` files). Commented-out
includes are ignored. For each `DYNAMIC` entry, read the code to work out the
target; if you cannot be sure, it is an open item, not a guess.

Then find who else uses the in-folder files, because a shared include changed
here affects pages outside the folder:

```bash
grep -rn --include='*.php' -e 'page_header.php' -e 'includes.php' . | grep -v '^./<session-folder>/'
```

List every outside caller as an open item ("also included by X; that folder
must be migrated or re-tested").

## Step 3 — Read before editing

Read every file in the tree (including outside-folder ones, read-only) before
changing anything. Includes share state: `includes.php` may open the DB
connection that `index.php` uses as `$conn`, `page_header.php` may start the
session, a helper may define functions other files call. Note these shared
names, because renaming or restructuring them in one file breaks the others.

Read only the section of `php84-migrate-folder.prompt.md` you need (tool usage,
then whichever topic comes up), never the whole file.

## Step 4 — Write the task file and plan

Create `<session-folder>/<entry-name>-tasks.md` (not committed) with:

```markdown
# <entry.php> → PHP 8.4.27
Session folder: <folder>
## Edit order
1. [ ] index.php
2. [ ] page_header.php
...
## Outside folder (read-only this session)
- ../db_config/config_acdcs.php — needs: ...
## Open items
- ...
```

Show the user the edit order, the outside-folder list and the open items so
far, then continue to the dry-run.

## Step 5 — Dry-run the tool

Get the exact command line from the tool-usage section of
`php84-migrate-folder.prompt.md` (or `php tools/php53-to-84-refactor.php --help`).
Do not invent arguments. Run it on the session folder, or on the in-folder files
of the tree if the tool accepts files, with `--quiet --no-backticks` and without
`--write`.

Summarise the result per file (what it would change, what it flagged), then
**stop and ask for approval**. Do not run `--write` in the same turn.

## Step 6 — Apply and finish by hand

After approval, run the same command with `--write`. Then go through the files
in edit order, **index.php first**, fixing what the tool left, using
`references/checklist.md` (removed functions, mysql_* → PDO with the recorded
options, binding and LIMIT/OFFSET clamping, sort allowlists, MySQL 8 reserved
words, error handling, passwords, silent 8.x behaviour changes). Read only the
checklist section a file needs.

For each file:
1. Make the edits; keep shared variable/function names from Step 3 intact.
2. `php -l <file>` with PHP 8.4.27.
3. Tick it in the task file; add any new open items.

When the tree shares one DB connection, convert it once where it is created and
update the callers; do not open extra connections per file.

## Step 7 — Report

End with a short summary:
- Files changed (in folder), each with one line on what changed.
- `php -l` result per file.
- Files outside the folder that still need migrating, and who includes them.
- Open items, new ones only (see Step 0), in two groups:
  - **Needs a decision**: affects every folder, such as where `session_start()`
    goes or the error response shape. For each, give the evidence, the options,
    and suggested `CLAUDE.md` Decisions wording.
  - **Follow-up**: belongs to a specific file or folder, such as outside
    callers, dynamic includes, `stripslashes` sites, bugs found in files
    outside the folder, or unreferenced actions flagged for removal. Give the
    file, the line, and which folder's session should handle it.
- One line for the remaining project-wide items from `CLAUDE.md`.

Do not commit. Leave committing to the user.