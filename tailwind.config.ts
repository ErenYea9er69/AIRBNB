import type { Config } from "tailwindcss";

const config: Config = {
  content: [
    "./app/**/*.{js,ts,jsx,tsx,mdx}",
    "./components/**/*.{js,ts,jsx,tsx,mdx}",
  ],
  theme: {
    extend: {
      fontFamily: {
        rubik: ["var(--font-rubik)", "sans-serif"],
        "rubik-bold": ["var(--font-rubik-bold)", "sans-serif"],
        "rubik-extrabold": ["var(--font-rubik-extrabold)", "sans-serif"],
        "rubik-medium": ["var(--font-rubik-medium)", "sans-serif"],
        "rubik-semibold": ["var(--font-rubik-semibold)", "sans-serif"],
        "rubik-light": ["var(--font-rubik-light)", "sans-serif"],
      },
      colors: {
        primary: {
          100: "#0061FF0A",
          200: "#0061FF1A",
          300: "#0061FF",
        },
        accent: {
          100: "#FBFBFD",
        },
        black: {
          DEFAULT: "#000000",
          100: "#8C8E98",
          200: "#666876",
          300: "#191D31",
        },
        danger: "#F75555",
      },
    },
  },
  plugins: [],
};
export default config;
