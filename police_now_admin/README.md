# Police Now Admin Portal

This is the administrative interface for the Police Now application, built with React, TypeScript, and Material-UI.

## Prerequisites

- Node.js (version 16.x or higher)
- npm (comes with Node.js)

## Setup Instructions

1. **Install Dependencies**
   ```bash
   # Navigate to the project directory
   cd police_now_admin

   # Install dependencies
   npm install
   ```

2. **Start Development Server**
   ```bash
   npm run dev
   ```
   The application will be available at `http://localhost:5173`

3. **Build for Production**
   ```bash
   npm run build
   ```

4. **Preview Production Build**
   ```bash
   npm run preview
   ```

## Project Structure

```
police_now_admin/
├── src/              # Source files
│   ├── assets/       # Static assets
│   ├── components/   # React components
│   ├── App.tsx       # Main application component
│   └── main.tsx      # Application entry point
├── public/           # Public assets
├── package.json      # Project configuration
└── tsconfig.json     # TypeScript configuration
```

## Available Scripts

- `npm run dev` - Start development server
- `npm run build` - Build for production
- `npm run preview` - Preview production build
- `npm run lint` - Run ESLint

## Troubleshooting

If you encounter any issues:

1. **Dependencies Issues**
   ```bash
   # Remove node_modules and reinstall
   rm -rf node_modules
   npm install
   ```

2. **TypeScript Errors**
   ```bash
   # Clear TypeScript cache
   rm -rf node_modules/.cache/typescript
   ```

3. **Vite Issues**
   ```bash
   # Clear Vite cache
   rm -rf node_modules/.vite
   ```

## Development

The application uses:
- React 18
- TypeScript
- Material-UI for components
- Vite for build tooling

## Support

For any issues or questions, please contact the development team.
