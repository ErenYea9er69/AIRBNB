"use client";

import React, { useState } from "react";
import { useSearchParams, useRouter, usePathname } from "next/navigation";
import { categories } from "@/constants/data";

const Filters = () => {
  const pathname = usePathname();
  const searchParams = useSearchParams();
  const { replace } = useRouter();
  const [selectedCategory, setSelectedCategory] = useState(
    searchParams.get("filter") || "All"
  );

  const handleCategoryPress = (category: string) => {
    const params = new URLSearchParams(searchParams);
    
    if (selectedCategory === category) {
      setSelectedCategory("");
      params.delete("filter");
    } else {
      setSelectedCategory(category);
      params.set("filter", category);
    }
    
    replace(`${pathname}?${params.toString()}`, { scroll: false });
  };

  return (
    <div className="flex flex-row items-center gap-4 overflow-x-auto no-scrollbar mt-3 mb-2 py-2">
      {categories.map((item, index) => (
        <button
          onClick={() => handleCategoryPress(item.category)}
          key={index}
          className={`flex flex-col items-start whitespace-nowrap px-4 py-2 rounded-full transition-all duration-200 ${
            selectedCategory === item.category
              ? "bg-primary-300"
              : "bg-primary-100 border border-primary-200 hover:bg-primary-200"
          }`}
        >
          <span
            className={`text-sm ${
              selectedCategory === item.category
                ? "text-white font-rubik-bold mt-0.5"
                : "text-black-300 font-rubik"
            }`}
          >
            {item.title}
          </span>
        </button>
      ))}
    </div>
  );
};

export default Filters;
