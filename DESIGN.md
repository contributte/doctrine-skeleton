# Doctrine Skeleton Design

Doctrine Skeleton has one page: a users table with buttons that create and delete users. A developer who has
installed the skeleton sees it to check that both databases work.

## Principles

- **The page proves the wiring, nothing more.** It shows data from both entity managers and one write action, so a
  developer sees Doctrine working in under a minute. It is not a UI kit.
- **No build step.** Styles are Tailwind utility classes loaded from `cdn.tailwindcss.com`; there is no CSS file,
  no JavaScript of our own and no `package.json`.
- **Few templates.** One layout and one view; error pages come from `contributte/nella`.

## Inventory

- Layout with title, flash messages, content and footer: `app/UI/@Templates/@layout.latte`
- Home page, users table and the two action links: `app/UI/Home/Templates/default.latte`
- Presenter and signals `createUser!`, `deleteUsers!`: `app/UI/Home/HomePresenter.php`
- Error pages 403, 404, 405, 410, 4xx, 500 and 503: `src/UI/Templates/` in `contributte/nella`, not in this repository
- Tracy bar with the DBAL panel for both connections in debug mode: `config/doctrine.neon` (`debug.panel`)

## Layout

- One centered column: `max-w-xl` below 768 px, `md:max-w-4xl` above, with `px-6` side padding.
- Sections are stacked with `divide-y` and `py-8`: heading, flash messages, content, footer.
- The table sits in `overflow-x-auto` and fills the column (`w-full`).

## Typography

- Tailwind's default sans-serif stack with `antialiased`.
- `text-3xl` bold page title, `text-2xl` bold section title, `text-sm` table body, `text-xs` uppercase table header.

## Colors and Tokens

- All colors are Tailwind classes in the two templates; there is no config or token file.
- Create is `bg-blue-500`, remove is `bg-red-500`, both with white bold text.
- Flash messages: `bg-green-500` for `success`, `bg-blue-500` for `info`, white text for every type.

## States

- Empty: the table renders its header and no rows; there is no "no users" message.
- Loading: none; every action is a full page load.
- Success: "Saved" or "Removed" flash after the redirect. Both use the default `info` type, so both are blue.
- Error: a database that is down or not migrated breaks the whole page with Tracy (debug) or the Nella 500 page.
  A flash of any type other than `success` or `info` has no background, so its white text is invisible.

## Accessibility

- The table uses `th scope="col"` and `th scope="row"`; actions are real links.
- Known gaps: the viewport meta sets `user-scalable=no` and `maximum-scale=1.0`, so mobile users cannot zoom.
  `<html>` has no `lang`. "Remove users" deletes every PostgreSQL user on a plain GET link with no confirmation.

## Dark Mode

- Partial. The table has `dark:` classes and the Tailwind CDN follows the system setting, so the table
  turns dark while the page around it stays white.

## Responsive

- One breakpoint, Tailwind `md` (768 px), widens the column. The table scrolls horizontally when it does not fit.

## Screenshots

- `.docs/screenshot.png` is shown in `README.md`; the README header also embeds a live microlink screenshot of the
  demo. Take a new screenshot by hand from `make dev` when the page changes.

## Changing the UI

- Templates see Nette's `$user` (the security user), so the loop variable is `$_user`. Don't rename it to `$user`.
- `LatteTest` compiles every `*.latte` in `app/`; a template that does not compile fails `make tests`.
- User IDs from the two databases can repeat in the merged table, and no column says which database a row came
  from.
- Keep the page free of a frontend build; `PRD.md` lists that as a non-goal.

## Checklist

- [ ] `make tests` passes, including `LatteTest`
- [ ] The page renders with both databases running and migrated
- [ ] New flash types have a background class in `@layout.latte`
- [ ] Checked at 375 px and 1280 px width
- [ ] `.docs/screenshot.png` updated if the page looks different
