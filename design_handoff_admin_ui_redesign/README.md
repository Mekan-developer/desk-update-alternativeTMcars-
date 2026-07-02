# Handoff: Admin Panel Redesign — Push-уведомления + Layout System

## Overview
Redesign of the "Доска объявлений" admin panel, focused on the Push-уведомления (push notifications) screen and the shared shell around it (sidebar nav + page header). Goal: cleaner hierarchy, consistent spacing/icons, and a notification preview panel that didn't exist in the original screen. Three visual directions were explored so the team can pick a base or mix elements.

## About the Design Files
The bundled files (`Admin UI.dc.html`, `Icon.dc.html`) are **design references built as self-contained HTML prototypes** — they render directly in a browser to show intended look, spacing, and behavior. They are **not production code to copy in verbatim**. Your task is to **recreate this design in the target codebase's existing stack** (React, Vue, whatever the admin already uses) using its existing component library / patterns. If there is no existing admin frontend stack yet, pick whatever framework fits the project and implement fresh.

`Admin UI.dc.html` is a custom "Design Component" format specific to this design tool — open it in a browser to see it live, but treat its markup as a reference/spec, not a literal file to drop in. `Icon.dc.html` is the icon sub-component it imports (a small SVG icon-by-name renderer) — its `ICONS` path data (in the `<script>` block) is genuinely reusable and worth copying as-is into an `Icon.tsx`/`Icon.jsx` equivalent.

## Fidelity
**High-fidelity.** Colors, spacing, typography, and radii below are final for whichever direction you pick — recreate pixel-accurately. The three options are not "pick one exactly" — the team may ask for a mix (e.g., 1a's layout with 1b's header).

## Screens / Views

There is one screen — **Push-уведомления** — shown in 3 theme directions, each a full page (sidebar + header + content). All three share identical structure; only the CSS custom-property values (color tokens) and a couple of stylistic details differ per direction. Structure below applies to all three unless noted.

### Layout — page shell
- Full-bleed flex row: `sidebar` (252px fixed) + `content` (flex: 1, min-width: 0).
- Sidebar: `display:flex; flex-direction:column; padding:18px 14px 16px`.
- Content column: `display:flex; flex-direction:column`.

### Sidebar contents (top → bottom)
1. **Brand block**: 34×34px rounded-square (radius 9px) icon tile in accent color containing a "menu" (hamburger) icon (white, 17px) + two-line label stack: "Доска" (14.5px/700) over "объявлений" (11px, muted). Row gap 10px, padding `4px 6px 22px`.
2. **Nav groups**, each: an 10.5px/700 uppercase section label (letter-spacing .07em, muted color, padding `16px 10px 6px` — `8px 10px 6px` for the first group) followed by nav rows.
3. **Nav row** (inactive): flex row, gap 10px, padding `8px 10px`, border-radius 8px, 13.5px/500 text, 17px icon. Hover background = `--nav-hover` token.
4. **Nav row (active — "Push-уведомления")**: same box but padding `9px 10px`, radius 9px, filled with the accent color (or accent-tint pill, see per-direction notes), bold (600–700) text.
5. Groups in order: **ГЛАВНОЕ** (Dashboard, Пользователи, Объявления, Ролики, Чат) → **КОНТЕНТ** (Категории, Регионы и города, Новости) → **МОДЕРАЦИЯ** (Жалобы, Отзывы) → **СИСТЕМА** (Тарифы, Статистика, **Push-уведомления ← active**) → **МОНИТОРИНГ** (Очереди — red status dot, WS — green status dot; these two are plain 8px filled circles, not icons).
6. Bottom: collapse chevron button (28×28px, radius 8px), pushed down via `margin-top:auto`, right-aligned.

### Content column
- **Header row**: padding `20px 32px`, bottom border, flex row space-between. Left: 12px muted eyebrow "Система" over 21px/700 title "Push-уведомления". Right: secondary button "История рассылок" (ghost / soft-shadow depending on direction).
- **Body**: padding `28px 32px` (direction 1b uses `20px 32px 32px` under its extra top bar), flex row, gap 24px, align-items flex-start.
  - **Form card** (`flex: 1 1 520px`): white/dark card, radius 14–16px, padding 26–28px, flex column gap 20px. Contains, top to bottom:
    1. Card title "Отправить уведомление" (15px/700) + helper line "Придёт как push и сохранится в истории рассылок" (12.5px, muted).
    2. **Заголовок** field: label (12.5px/600) + text input, placeholder "Заголовок уведомления".
    3. **Текст** field: label + 3-row textarea, placeholder "Текст уведомления".
    4. **Получатели** field: label + segmented control (3 pills: "Все пользователи" / "Сегмент" / "Один пользователь", 4px padding track, 7px/14px pill padding, 13px/600) + a muted 12px helper line under it that changes with the selection (see Interactions).
    5. **Тип ссылки / ID ссылки** row: flex row gap 14px — select (flex:1) with options `— нет —`, `Объявление`, `Профиль пользователя`, `Категория`; and a 160px-wide "ID ссылки" text input (monospace, `IBM Plex Mono`) that is disabled/greyed out whenever "Тип ссылки" = `— нет —`.
    6. **Footer actions**: top border, flex row right-aligned, gap 10px — ghost button "Тестовая отправка" + primary button "Отправить" (accent bg, white text, send/paper-plane icon, drop shadow tinted with the accent color).
  - **Preview card** (300px fixed width): "Предпросмотр" title (13px/700) + a mock push-notification tile (36×36 accent icon tile with bell icon + app name "Доска объявлений" / timestamp "сейчас" / bold title "Заголовок уведомления" / 12.5px body copy) + a 12px muted caption "Так уведомление будет выглядеть на устройстве получателя." Direction 1c renders this tile with a translucent/blurred background to read as an on-device lock-screen notification.

## Interactions & Behavior
Implemented live in direction **1a** only (1b/1c show the same UI statically, defaulted to "Все пользователи" / "— нет —"):
- **Получатели segmented control**: clicking a pill sets it active (filled accent bg, white text) and deactivates the others (transparent bg, secondary text color). The helper caption below updates per selection:
  - "Все пользователи" → "128 430 пользователей получат уведомление"
  - "Сегмент" → "Выберите сегмент во вкладке «Сегменты» — сейчас: Активные за 30 дней (24 118)"
  - "Один пользователь" → "Укажите ID пользователя ниже, уведомление получит только он"
- **Тип ссылки select**: changing away from `— нет —` enables the "ID ссылки" input (normal field bg/text color); selecting `— нет —` disables it and greys it out (bg `#F1F1F5` light theme / darker equivalent on dark themes, text = muted token).
- Inputs/textareas/selects: on focus, border color → accent, plus a 3px accent-tinted glow ring (`box-shadow: 0 0 0 3px var(--accent-tint)`).
- Nav rows and icon-only buttons: hover background = `--nav-hover` token.
- Primary "Отправить" button: hover background = `--accent-hover` token.

No navigation between screens, no form submission wiring, no real data — this is a static/light-interactive visual spec.

## State Management
Minimal — only needed for the notification composer:
- `recipient: 'all' | 'segment' | 'one'` (default `'all'`) — drives segmented control + helper caption.
- `linkType: 'none' | 'listing' | 'profile' | 'category'` (default `'none'`) — drives the ID field's enabled/disabled state.
In a real build you'd also want: form field values (title/body/recipient-detail/link id), submit/loading/success/error states for the send action, and a fetched recipient count for the "Все пользователи" caption.

## Design Tokens

Shared across all directions:
- **Font**: `Golos Text` (400/500/600/700/800) for all UI text — chosen for solid Cyrillic support and to avoid generic/overused sans fonts. `IBM Plex Mono` (500/600) for the numeric "ID ссылки" field only.
- **Radii**: nav rows 8–9px · inputs 9–10px · cards 14–16px · icon tiles 9–10px · pills 7–8px.
- **Accent (indigo/violet family, matches original brand)**: base `#4F46E5` (light-theme directions), brighter `#6D63F2` (dark direction, for contrast against dark surfaces); hover states one step darker/lighter respectively.

### 1a — "Рефайн" (dark sidebar / light content, closest to original)
- `--sidebar-bg:#181d2f` · `--sidebar-border:rgba(255,255,255,.08)` · `--sidebar-text:rgba(255,255,255,.72)` · `--sidebar-text-strong:#ffffff` · `--sidebar-muted:rgba(255,255,255,.42)` · `--section-label:rgba(255,255,255,.32)` · `--nav-hover:rgba(255,255,255,.06)`
- `--accent:#4F46E5` · `--accent-hover:#4338CA` · `--accent-tint:oklch(93% 0.03 276)`
- `--content-bg:#F7F7FA` · `--card-bg:#ffffff` · `--card-border:#E7E7ED`
- `--text:#15172B` · `--text-secondary:#5A5D72` · `--text-muted:#9497A8`
- `--field-bg:#ffffff` · `--field-border:#DCDDE6`
- Active nav item: solid accent fill, white text.

### 1b — "Светлая современная" (full light theme, adds top bar)
- `--sidebar-bg:#ffffff` · `--sidebar-border:#EAEAF0` · `--sidebar-text:#53566B` · `--sidebar-text-strong:#15172B` · `--sidebar-muted:#9497A8` · `--section-label:#B3B5C4` · `--nav-hover:#F4F4F8`
- `--accent:#4F46E5` · `--accent-hover:#4338CA` · `--accent-tint:#EEEDFB`
- `--content-bg:#FAFAFC` · `--card-bg:#ffffff` · `--card-border:#EAEAF0`
- `--text:#15172B` · `--text-secondary:#5A5D72` · `--text-muted:#9497A8`
- `--field-bg:#FAFAFC` · `--field-border:#E3E4EC`
- Active nav item: accent-tint pill background, accent-colored text (not solid fill).
- Adds a 58px top bar above the page header: search field (left, 280px, with search icon + placeholder "Поиск по разделу…") and, right-aligned, a bell icon button (with a small red unread dot) + a 34px circular avatar with initials "АД" on accent-tint background.
- Cards are borderless with a soft ambient shadow (`0 8px 24px rgba(20,20,40,.06)`) instead of a 1px border, radius bumped to 16px.

### 1c — "Полностью тёмная" (dark everywhere, brighter accent)
- `--sidebar-bg:#14172A` · `--sidebar-border:rgba(255,255,255,.08)` · `--sidebar-text:rgba(255,255,255,.68)` · `--sidebar-text-strong:#F5F5FA` · `--sidebar-muted:rgba(255,255,255,.4)` · `--section-label:rgba(255,255,255,.3)` · `--nav-hover:rgba(255,255,255,.05)`
- `--accent:#6D63F2` · `--accent-hover:#7C73F5` · `--accent-tint:rgba(109,99,242,.16)`
- `--content-bg:#1B1E33` · `--card-bg:#20233B` · `--card-border:rgba(255,255,255,.08)`
- `--text:#F1F1F6` · `--text-secondary:#A6A8BC` · `--text-muted:#787C94`
- `--field-bg:#181B2E` · `--field-border:rgba(255,255,255,.1)`
- Active nav item + primary button get an extra soft accent glow ring (`0 0 0 4px var(--accent-tint)`).
- Status dots (Очереди/WS) get a matching color glow (`box-shadow: 0 0 6px <color>`).
- Preview card's notification tile uses a translucent `rgba(255,255,255,.06)` background with `backdrop-filter: blur(6px)` to read as an on-device lock-screen card.

## Assets
No raster images. All icons are hand-built inline SVGs (17–18px, `stroke="currentColor"`, `stroke-width:1.7`, round caps/joins), so they recolor automatically with the surrounding text color / theme tokens. Icon set and exact path data live in `Icon.dc.html`'s `ICONS` object — copy that map directly into your icon component. Icons used: `grid` (Dashboard), `users` (Пользователи), `listing` (Объявления), `video` (Ролики), `chat` (Чат), `tag` (Категории), `pin` (Регионы и города), `news` (Новости), `flag` (Жалобы), `star` (Отзывы), `coin` (Тарифы), `chart` (Статистика), `bell` (Push-уведомления / preview tile), `menu` (brand tile), `chevronLeft` (collapse), `search` (1b top bar), `send` (submit button). Status dots (Очереди/WS) are plain colored circles, no icon needed.

## Files
- `Admin UI.dc.html` — the 3 full-page design directions (1a/1b/1c), side by side.
- `Icon.dc.html` — the icon-by-name sub-component with all path data.

Open `Admin UI.dc.html` in a browser to view/interact with the live reference (option 1a has working recipient-selector + link-type-toggle interactions).
