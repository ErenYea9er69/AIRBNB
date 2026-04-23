"use client";

import React from "react";
import Link from "next/link";
import Image from "next/image";
import { usePathname } from "next/navigation";
import icons from "@/constants/icons";

const Navbar = () => {
  const pathname = usePathname();

  const navItems = [
    { name: "Home", href: "/", icon: icons.home },
    { name: "Explore", href: "/explore", icon: icons.search },
    { name: "Profile", href: "/profile", icon: icons.person },
  ];

  return (
    <nav className="fixed bottom-0 md:top-0 md:bottom-auto left-0 right-0 bg-white border-t md:border-t-0 md:border-b border-gray-100 z-50 px-6 py-2 md:py-4 shadow-sm backdrop-blur-md bg-white/90">
      <div className="max-w-7xl mx-auto flex items-center justify-between">
        <div className="hidden md:flex items-center gap-2">
            <div className="w-10 h-10 bg-primary-300 rounded-lg flex items-center justify-center">
                <span className="text-white font-rubik-extrabold text-xl">R</span>
            </div>
            <span className="text-xl font-rubik-bold text-black-300">Restate</span>
        </div>

        <div className="flex flex-1 md:flex-initial items-center justify-around md:justify-end gap-1 md:gap-8">
          {navItems.map((item) => {
            const isActive = pathname === item.href;
            return (
              <Link
                key={item.href}
                href={item.href}
                className={`flex flex-col md:flex-row items-center gap-1 md:gap-2 px-4 py-1.5 rounded-full transition-all duration-300 ${
                  isActive 
                    ? "text-primary-300 md:bg-primary-100/50" 
                    : "text-black-100 hover:text-black-300"
                }`}
              >
                <Image 
                    src={item.icon} 
                    alt={item.name} 
                    width={20} 
                    height={20} 
                    className={`w-5 h-5 ${isActive ? "" : "grayscale"}`}
                />
                <span className={`text-[10px] md:text-sm font-rubik-medium ${isActive ? "" : "hidden md:block"}`}>
                    {item.name}
                </span>
                {isActive && <div className="md:hidden w-1 h-1 bg-primary-300 rounded-full" />}
              </Link>
            );
          })}
        </div>
      </div>
    </nav>
  );
};

export default Navbar;
