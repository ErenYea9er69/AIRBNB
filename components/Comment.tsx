import React from "react";
import Image from "next/image";
import icons from "@/constants/icons";
import { Models } from "appwrite";

interface Props {
  item: Models.Document & any;
}

const Comment = ({ item }: Props) => {
  return (
    <div className="flex flex-col items-start p-4 bg-gray-50 rounded-2xl w-full">
      <div className="flex flex-row items-center w-full">
        <div className="relative w-12 h-12 rounded-full overflow-hidden shrink-0">
          {item.avatar && <Image src={item.avatar} alt={item.name} fill className="object-cover" />}
        </div>
        <div className="flex flex-col ml-3">
            <span className="text-base text-black-300 font-rubik-bold">
                {item.name}
            </span>
            <span className="text-xs text-black-100 font-rubik">
                Verified Resident
            </span>
        </div>
      </div>

      <p className="text-black-200 text-base font-rubik mt-3 leading-relaxed">
        {item.review}
      </p>

      <div className="flex flex-row items-center w-full justify-between mt-4 border-t border-gray-200/50 pt-3">
        <div className="flex flex-row items-center cursor-pointer hover:opacity-70 transition-opacity">
          <Image
            src={icons.heart}
            alt="heart"
            width={16}
            height={16}
            className="w-4 h-4 brightness-0 transition-all duration-300"
            style={{ filter: "invert(24%) sepia(99%) saturate(4605%) hue-rotate(215deg) brightness(101%) contrast(105%)" }}
          />
          <span className="text-black-300 text-sm font-rubik-medium ml-2">
            120
          </span>
        </div>
        <span className="text-black-100 text-xs font-rubik">
          {new Date(item.$createdAt).toLocaleDateString(undefined, {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
          })}
        </span>
      </div>
    </div>
  );
};

export default Comment;
