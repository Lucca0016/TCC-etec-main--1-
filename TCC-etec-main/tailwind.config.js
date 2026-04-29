module.exports = {
  content: [
    './src/**/*.{js,jsx,ts,tsx}',
    './resources/views/**/*.{php,html}',
  ],
  theme: {
    extend: {
      colors: {
        azul: {
          50: '#f0f7ff',
          600: '#2563eb',
          700: '#1d4ed8',
        },
        cinza: {
          50: '#f9fafb',
          200: '#e5e7eb',
          900: '#111827',
        },
      },
      shadows: {
        sm: '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
        md: '0 4px 6px -1px rgba(0, 0, 0, 0.1)',
        lg: '0 10px 15px -3px rgba(0, 0, 0, 0.1)',
      },
      borderRadius: {
        lg: '0.5rem',
      },
    },
  },
  plugins: [],
};
