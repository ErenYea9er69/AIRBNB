"use client";

import React from "react";
import Image from "next/image";
import { logout } from "@/lib/appwrite";
import { useGlobalContext } from "@/lib/global-provider";
import icons from "@/constants/icons";
import { settings } from "@/constants/data";
import Navbar from "@/components/Navbar";

interface SettingsItemProp {
  icon: any;
  title: string;
  onPress?: () => void;
  textStyle?: string;
  showArrow?: boolean;
}

const SettingsItem = ({
  icon,
  title,
  onPress,
  textStyle,
  showArrow = true,
}: SettingsItemProp) => (
  <button
    onClick={onPress}
    className="flex flex-row items-center justify-between py-4 w-full hover:bg-gray-50 px-2 rounded-xl transition-colors group"
  >
    <div className="flex flex-row items-center gap-4">
      <div className="w-10 h-10 bg-gray-50 rounded-full flex items-center justify-center group-hover:bg-white transition-colors">
        <Image src={icon} alt={title} width={24} height={24} className="w-6 h-6" />
      </div>
      <span className={`text-lg font-rubik-medium text-black-300 ${textStyle}`}>
        {title}
      </span>
    </div>

    {showArrow && (
      <Image src={icons.rightArrow} alt="arrow" width={20} height={20} className="w-5 h-5 opacity-50" />
    )}
  </button>
);

const Profile = () => {
  const { user, refetch } = useGlobalContext();

  const handleLogout = async () => {
    const result = await logout();
    if (result) {
      refetch();
    } else {
      alert("Failed to logout");
    }
  };

  return (
    <div className="min-h-screen bg-white pb-24 md:pb-0 md:pt-20">
      <Navbar />

      <main className="max-w-3xl mx-auto px-6 py-10">
        <div className="flex flex-row items-center justify-between">
          <h1 className="text-2xl font-rubik-bold text-black-300">Profile</h1>
          <button className="p-2 hover:bg-gray-100 rounded-full transition-colors">
            <Image src={icons.bell} alt="bell" width={24} height={24} className="w-6 h-6" />
          </button>
        </div>

        <div className="flex flex-col items-center mt-10">
          <div className="relative">
            {user?.avatar && (
              <div className="relative w-44 h-44 rounded-full overflow-hidden border-4 border-primary-100 shadow-xl">
                <Image
                  src={user.avatar}
                  alt={user.name}
                  fill
                  className="object-cover"
                />
              </div>
            )}
            <button className="absolute bottom-2 right-2 bg-primary-300 p-2.5 rounded-full shadow-lg hover:scale-110 transition-transform">
              <Image src={icons.edit} alt="edit" width={20} height={20} className="w-5 h-5 invert brightness-0" />
            </button>
          </div>

          <h2 className="text-3xl font-rubik-bold mt-6 text-black-300">{user?.name}</h2>
          <p className="text-black-100 font-rubik mt-1">{user?.email}</p>
        </div>

        <div className="flex flex-col mt-12 bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
          <SettingsItem icon={icons.calendar} title="My Bookings" />
          <SettingsItem icon={icons.wallet} title="Payments" />
        </div>

        <div className="flex flex-col mt-6 bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
          {settings.slice(2).map((item, index) => (
            <SettingsItem key={index} {...item} />
          ))}
        </div>

        <div className="flex flex-col mt-6 bg-red-50/50 rounded-2xl border border-red-100 p-4 shadow-sm">
          <SettingsItem
            icon={icons.logout}
            title="Logout"
            textStyle="text-danger"
            showArrow={false}
            onPress={handleLogout}
          />
        </div>
      </main>
    </div>
  );
};

export default Profile;
