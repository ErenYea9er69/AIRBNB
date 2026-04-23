"use client";

import React, { useState } from "react";
import Image from "next/image";
import { useSearchParams, useRouter, usePathname } from "next/navigation";
import { useDebouncedCallback } from "use-debounce";
import icons from "@/constants/icons";

const Search = () => {
  const pathname = usePathname();
  const searchParams = useSearchParams();
  const { replace } = useRouter();
  const [search, setSearch] = useState(searchParams.get("query")?.toString() || "");

  const debouncedSearch = useDebouncedCallback((text: string) => {
    const params = new URLSearchParams(searchParams);
    if (text) {
      params.set("query", text);
    } else {
      params.delete("query");
    }
    replace(`${pathname}?${params.toString()}`);
  }, 500);

  const handleSearch = (e: React.ChangeEvent<HTMLInputElement>) => {
    const text = e.target.value;
    setSearch(text);
    debouncedSearch(text);
  };

  return (
    <div className="flex flex-row items-center justify-between w-full px-4 rounded-lg bg-accent-100 border border-primary-100 mt-5 py-3">
      <div className="flex-1 flex flex-row items-center justify-start z-50">
        <Image src={icons.search} alt="search" width={20} height={20} className="w-5 h-5" />
        <input
          type="text"
          value={search}
          onChange={handleSearch}
          placeholder="Search for anything"
          className="text-sm font-rubik text-black-300 ml-2 flex-1 bg-transparent outline-none border-none focus:ring-0"
        />
      </div>

      <button className="hover:opacity-70 transition-opacity">
        <Image src={icons.filter} alt="filter" width={20} height={20} className="w-5 h-5" />
      </button>
    </div>
  );
};

export default Search;
