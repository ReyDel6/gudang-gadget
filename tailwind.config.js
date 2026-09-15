/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'sans-serif'],
            },
            colors: {
                navy: {
                    50: '#EFF3F8',
                    100: '#D7E1EC',
                    400: '#3D6491',
                    600: '#274A70',
                    700: '#1E3A5F',
                    800: '#152A45',
                    900: '#0E1D30',
                },
                gold: {
                    50: '#FDF3E7',
                    400: '#E8A33D',
                    500: '#D97706',
                    600: '#B35F04',
                },
            },
        },
    },
    plugins: [],
}
