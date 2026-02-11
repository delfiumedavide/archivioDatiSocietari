/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    50: '#f0f4f8',
                    100: '#d9e2ec',
                    200: '#bcccdc',
                    300: '#9fb3c8',
                    400: '#829ab1',
                    500: '#627d98',
                    600: '#486581',
                    700: '#334e68',
                    800: '#243b53',
                    900: '#1e3a5f',
                    950: '#102a43',
                },
                gold: {
                    50: '#fef9ec',
                    100: '#fcf0cc',
                    200: '#f9e099',
                    300: '#f5cb57',
                    400: '#f2b830',
                    500: '#c9952b',
                    600: '#a87420',
                    700: '#8a571d',
                    800: '#72451e',
                    900: '#5f3a1e',
                },
            },
            fontFamily: {
                sans: ['Inter', 'system-ui', 'sans-serif'],
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
};
