module.exports = {
  content: [
    './templates/**/*.php',
    './assets/js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        primary: '#2563eb',
        secondary: '#0f766e',
        surface: '#ffffff',
        background: '#f8fafc',
        accent: '#f97316',
        danger: '#ef4444',
        success: '#16a34a',
      },
      boxShadow: {
        card: '0 16px 40px rgba(15, 23, 42, 0.08)',
      },
      borderRadius: {
        xl: '1rem',
      },
    },
  },
  plugins: [
    require('daisyui'),
  ],
  daisyui: {
    themes: ['light', 'dark'],
  },
};
