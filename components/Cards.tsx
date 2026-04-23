"use client";

import React from "react";
import Image from "next/image";
import icons from "@/constants/icons";
import images from "@/constants/images";
import { Models } from "appwrite";

interface Property extends Models.Document {
  name: string;
  address: string;
  price: number;
  rating: number;
  image: string;
  type: string;
}

interface Props {
  item: Models.Document & any;
  onPress?: () => void;
}

export const FeaturedCard = ({ item, onPress }: Props) => {
  return (
    <div
      onClick={onPress}
      className="flex flex-col items-start w-64 h-96 relative cursor-pointer group shrink-0"
    >
      <div className="w-full h-full relative rounded-2xl overflow-hidden">
        {item.image && (
          <Image
            src={item.image}
            alt={item.name}
            fill
            className="object-cover group-hover:scale-110 transition-transform duration-500"
          />
        )}
        <Image
          src={images.cardGradient}
          alt="gradient"
          fill
          className="absolute bottom-0 object-cover"
        />
      </div>

      <div className="flex flex-row items-center bg-white/90 px-3 py-1.5 rounded-full absolute top-5 right-5 backdrop-blur-sm z-10">
        <Image src={icons.star} alt="star" width={14} height={14} className="w-3.5 h-3.5" />
        <span className="text-xs font-rubik-bold text-primary-300 ml-1">
          {item.rating}
        </span>
      </div>

      <div className="flex flex-col items-start absolute bottom-5 inset-x-5 z-10">
        <h2 className="text-xl font-rubik-extrabold text-white truncate w-full">
          {item.name}
        </h2>
        <p className="text-base font-rubik text-white truncate w-full">
          {item.address}
        </p>

        <div className="flex flex-row items-center justify-between w-full mt-2">
          <span className="text-xl font-rubik-extrabold text-white">
            ${item.price}
          </span>
          <Image src={icons.heart} alt="heart" width={20} height={20} className="w-5 h-5 invert brightness-0" />
        </div>
      </div>
    </div>
  );
};

export const Card = ({ item, onPress }: Props) => {
  return (
    <div
      onClick={onPress}
      className="flex flex-col w-full mt-4 p-4 rounded-xl bg-white shadow-md hover:shadow-xl transition-shadow duration-300 cursor-pointer relative border border-gray-100 group"
    >
      <div className="flex flex-row items-center absolute px-2 py-1 top-6 right-6 bg-white/90 rounded-full z-10 backdrop-blur-sm border border-gray-100">
        <Image src={icons.star} alt="star" width={10} height={10} className="w-2.5 h-2.5" />
        <span className="text-[10px] font-rubik-bold text-primary-300 ml-0.5">
          {item.rating}
        </span>
      </div>

      <div className="relative w-full h-44 rounded-lg overflow-hidden">
        {item.image && (
          <Image
            src={item.image}
            alt={item.name}
            fill
            className="object-cover group-hover:scale-105 transition-transform duration-300"
          />
        )}
      </div>

      <div className="flex flex-col mt-3">
        <h3 className="text-base font-rubik-bold text-black-300 truncate">
          {item.name}
        </h3>
        <p className="text-xs font-rubik text-black-100 truncate">
          {item.address}
        </p>

        <div className="flex flex-row items-center justify-between mt-3">
          <span className="text-base font-rubik-bold text-primary-300">
            ${item.price}
          </span>
          <Image
            src={icons.heart}
            alt="heart"
            width={20}
            height={20}
            className="w-5 h-5 opacity-70 group-hover:opacity-100 transition-opacity"
          />
        </div>
      </div>
    </div>
  );
};
