module.exports = {
    darkMode: 'class',
    content: [
        "resources/views/index.blade.php",
        "resources/scripts/**/*.{vue,js,jsx,ts,tsx}"
    ],
    theme: {
        extend: {
            colors: {
                login: '#6159e6'
            },
            backgroundImage: {
                'login-page': "url('@/assets/login.svg')"
            }
        },
    },
    plugins: [],
}
