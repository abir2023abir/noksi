import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/View/Components/**/*.php',
        './app/Support/helpers.php',
        './app/Models/Order.php',
    ],
    safelist: [
        { pattern: /(bg|text|ring)-(amber|sky|violet|blue|emerald|rose|slate|orange)-(100|500|700|800|200)/ },
    ],
    theme: {
        container: {
            center: true,
            padding: { DEFAULT: '1.25rem', md: '2rem', xl: '3rem' },
            screens: { '2xl': '1440px' },
        },
        extend: {
            colors: {
                // Brand palette: deep plum, marigold, teal, terracotta, warm paper.
                plum: {
                    50: '#FBF4FA', 100: '#F5E5F2', 200: '#EACBE5', 300: '#D9A3CF', 400: '#C070B0',
                    500: '#A24A92', 600: '#84327A', 700: '#5B1B4F', 800: '#4A1642', 900: '#2E0C29', 950: '#1B0E2B',
                },
                marigold: {
                    50: '#FFFBEB', 100: '#FEF3C7', 200: '#FDE68A', 300: '#FCD34D', 400: '#F5C24B',
                    500: '#E9A916', 600: '#CA8A04', 700: '#A16207', 800: '#854D0E', 900: '#713F12',
                },
                teal: {
                    50: '#F0FDFA', 100: '#CCFBF1', 200: '#99F6E4', 300: '#5EEAD4', 400: '#2DD4BF',
                    500: '#14B8A6', 600: '#0D9488', 700: '#0F766E', 800: '#115E59', 900: '#134E4A',
                },
                terracotta: {
                    50: '#FFF7ED', 100: '#FFEDD5', 200: '#FED7AA', 300: '#FDBA74', 400: '#FB923C',
                    500: '#F97316', 600: '#EA580C', 700: '#C2410C', 800: '#9A3412', 900: '#7C2D12',
                },
                paper: {
                    DEFAULT: '#F7F2EA', 50: '#FDFBF7', 100: '#F7F2EA', 200: '#EFE6D8', 300: '#E3D5C0', 400: '#CDB899',
                },
                ink: { DEFAULT: '#14111A', 700: '#2A2433', 500: '#4B4456', 300: '#8B8497' },
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', '"Hind Siliguri"', ...defaultTheme.fontFamily.sans],
                display: ['Fraunces', '"Noto Serif Bengali"', 'Georgia', 'serif'],
                bangla: ['"Hind Siliguri"', '"Noto Serif Bengali"', 'sans-serif'],
                mono: ['"JetBrains Mono"', ...defaultTheme.fontFamily.mono],
            },
            fontSize: {
                'display-xl': ['clamp(3rem, 8vw, 7.5rem)', { lineHeight: '0.95', letterSpacing: '-0.03em' }],
                'display-lg': ['clamp(2.5rem, 6vw, 5rem)', { lineHeight: '1', letterSpacing: '-0.025em' }],
                'display-md': ['clamp(2rem, 4vw, 3.5rem)', { lineHeight: '1.05', letterSpacing: '-0.02em' }],
                'display-sm': ['clamp(1.5rem, 3vw, 2.25rem)', { lineHeight: '1.1', letterSpacing: '-0.015em' }],
            },
            spacing: { 4.5: '1.125rem', 13: '3.25rem', 15: '3.75rem', 18: '4.5rem' },
            minWidth: { 5: '1.25rem' },
            borderRadius: { '4xl': '2rem', '5xl': '2.5rem' },
            boxShadow: {
                soft: '0 2px 20px -4px rgb(20 17 26 / 0.08)',
                lift: '0 20px 50px -20px rgb(20 17 26 / 0.25)',
                card: '0 1px 2px rgb(20 17 26 / 0.04), 0 8px 24px -12px rgb(20 17 26 / 0.12)',
                glow: '0 0 0 4px rgb(245 194 75 / 0.25)',
            },
            animation: {
                marquee: 'marquee 40s linear infinite',
                'marquee-reverse': 'marquee-reverse 40s linear infinite',
                'spin-slow': 'spin 12s linear infinite',
                float: 'float 6s ease-in-out infinite',
                shimmer: 'shimmer 2s linear infinite',
                'pulse-ring': 'pulse-ring 1.8s cubic-bezier(0.2, 0.8, 0.2, 1) infinite',
            },
            keyframes: {
                marquee: { '0%': { transform: 'translateX(0)' }, '100%': { transform: 'translateX(-50%)' } },
                'marquee-reverse': { '0%': { transform: 'translateX(-50%)' }, '100%': { transform: 'translateX(0)' } },
                float: { '0%, 100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-12px)' } },
                shimmer: { '0%': { backgroundPosition: '-200% 0' }, '100%': { backgroundPosition: '200% 0' } },
                'pulse-ring': { '0%': { transform: 'scale(0.8)', opacity: '1' }, '100%': { transform: 'scale(1.8)', opacity: '0' } },
            },
            backgroundImage: {
                grain: "url(\"data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.06'/%3E%3C/svg%3E\")",
            },
            transitionTimingFunction: {
                'out-expo': 'cubic-bezier(0.16, 1, 0.3, 1)',
                'in-out-quart': 'cubic-bezier(0.76, 0, 0.24, 1)',
            },
        },
    },
    plugins: [forms({ strategy: 'class' }), typography],
};
