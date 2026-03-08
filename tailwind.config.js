export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        'dark-surface': {
          50: '#f8fafc',
          100: '#f1f5f9',
          200: '#e2e8f0',
          300: '#cbd5e1',
          400: '#94a3b8',
          500: '#64748b',
          600: '#475569',
          700: '#334155',
          800: '#1e293b',
          850: '#0f172a',
          900: '#020617',
          950: '#000000',
        }
      },
      transitionProperty: {
        'width': 'width',
        'height': 'height',
        'margin': 'margin',
        'padding': 'padding',
        'colors': 'background-color, border-color, color, fill, stroke',
        'transform': 'transform',
        'all': 'all',
      }
    },
  },
  plugins: [],
}

