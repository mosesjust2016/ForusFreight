{{-- Shared design tokens and base components (type, buttons, layout) used by
     the redesigned marketing pages. Wrap page content in .ui-page to use them. --}}
<style>
        .home,
        .ui-page {
            --paper: #f5f4ef;
            --paper-2: #ebe9e2;
            --ink: #0d1b20;
            --ink-2: #33444a;
            --muted: #56666c;
            --teal: #007f7f;
            --teal-deep: #005e5e;
            --teal-bright: #2dd4bf;
            --orange: #ff6200;
            --orange-hover: #ff7a26;
            --line: rgba(13, 27, 32, 0.12);
            --line-strong: rgba(13, 27, 32, 0.22);
            --display: 'Archivo', 'Inter', sans-serif;
            --mono: 'IBM Plex Mono', ui-monospace, monospace;
            --radius: 20px;

            background: var(--paper);
            color: var(--ink);
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
        }

        .h-wrap {
            width: 100%;
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .h-eyebrow {
            font-family: var(--mono);
            font-size: 0.8rem;
            font-weight: 500;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--teal-deep);
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
        }

        .h-eyebrow::before {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--orange);
        }

        .h-display {
            font-family: var(--display);
            font-stretch: 112%;
            font-weight: 800;
            letter-spacing: -0.025em;
            line-height: 1.02;
            text-wrap: balance;
        }

        .h-h2 {
            font-family: var(--display);
            font-stretch: 108%;
            font-weight: 800;
            font-size: clamp(2rem, 4vw, 3.1rem);
            letter-spacing: -0.02em;
            line-height: 1.05;
            text-wrap: balance;
        }

        .h-lead {
            font-size: 1.15rem;
            color: var(--ink-2);
            line-height: 1.6;
            text-wrap: pretty;
        }

        /* Buttons */
        .h-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            min-height: 52px;
            padding: 0 1.5rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            border: 2px solid transparent;
            cursor: pointer;
            transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
            white-space: nowrap;
        }

        .h-btn:active {
            transform: translateY(1px);
        }

        .h-btn--primary {
            background: var(--orange);
            color: var(--ink);
        }

        .h-btn--primary:hover {
            background: var(--orange-hover);
        }

        .h-btn--dark {
            background: var(--ink);
            color: #fff;
        }

        .h-btn--dark:hover {
            background: #000;
        }

        .h-btn--outline {
            border-color: var(--line-strong);
            color: var(--ink);
            background: transparent;
        }

        .h-btn--outline:hover {
            border-color: var(--ink);
        }

        .h-btn--light {
            background: #fff;
            color: var(--ink);
        }

        .h-btn--light:hover {
            background: var(--paper);
        }

        .h-btn .fa-arrow-right {
            transition: transform 0.2s ease;
        }

        .h-btn:hover .fa-arrow-right {
            transform: translateX(3px);
        }

    .ui-page .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }
</style>
