import React from "react";
import Image from "next/image";
import images from "@/constants/images";

const NoResults = () => {
  return (
    <div className="flex flex-col items-center my-10 w-full text-center">
      <div className="relative w-full max-w-md h-80">
        <Image
          src={images.noResult}
          alt="No result"
          fill
          className="object-contain"
        />
      </div>
      <h2 className="text-2xl font-rubik-bold text-black-300 mt-5">
        No Results Found
      </h2>
      <p className="text-base text-black-100 mt-2">
        We could not find any properties matching your criteria.
      </p>
    </div>
  );
};

export default NoResults;
