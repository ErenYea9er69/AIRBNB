import type { Metadata } from "next";
import localFont from "next/font/local";
import "./globals.css";
import GlobalProvider from "@/lib/global-provider";

const rubik = localFont({
  src: "../assets/fonts/Rubik-Regular.ttf",
  variable: "--font-rubik",
});
const rubikBold = localFont({
  src: "../assets/fonts/Rubik-Bold.ttf",
  variable: "--font-rubik-bold",
});
const rubikExtraBold = localFont({
  src: "../assets/fonts/Rubik-ExtraBold.ttf",
  variable: "--font-rubik-extrabold",
});
const rubikMedium = localFont({
  src: "../assets/fonts/Rubik-Medium.ttf",
  variable: "--font-rubik-medium",
});
const rubikSemiBold = localFont({
  src: "../assets/fonts/Rubik-SemiBold.ttf",
  variable: "--font-rubik-semibold",
});
const rubikLight = localFont({
  src: "../assets/fonts/Rubik-Light.ttf",
  variable: "--font-rubik-light",
});

export const metadata: Metadata = {
  title: "Restate | Premium Real Estate",
  description: "Find your ideal home with Restate",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en">
      <body
        className={`${rubik.variable} ${rubikBold.variable} ${rubikExtraBold.variable} ${rubikMedium.variable} ${rubikSemiBold.variable} ${rubikLight.variable} antialiased`}
      >
        <GlobalProvider>{children}</GlobalProvider>
      </body>
    </html>
  );
}
