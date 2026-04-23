"use client";

import React, { useEffect, Suspense } from "react";
import Image from "next/image";
import { useRouter, useSearchParams } from "next/navigation";
import { useAppwrite } from "@/lib/useAppwrite";
import { getProperties } from "@/lib/appwrite";

import icons from "@/constants/icons";
import Search from "@/components/Search";
import { Card } from "@/components/Cards";
import Filters from "@/components/Filters";
import NoResults from "@/components/NoResults";
import Navbar from "@/components/Navbar";

const ExploreContent = () => {
  const router = useRouter();
  const searchParams = useSearchParams();

  const query = searchParams.get("query") || "";
  const filter = searchParams.get("filter") || "All";

  const {
    data: properties,
    refetch,
    loading,
  } = useAppwrite({
    fn: getProperties,
    params: {
      filter: filter,
      query: query,
    },
    skip: true,
  });

  useEffect(() => {
    refetch({
      filter: filter,
      query: query,
    });
  }, [filter, query]);

  const handleCardPress = (id: string) => router.push(`/properties/${id}`);

  return (
    <div className="min-h-screen bg-white pb-24 md:pb-0 md:pt-20">
      <Navbar />

      <main className="max-w-7xl mx-auto px-6 py-10">
        <div className="flex flex-row items-center justify-between">
          <button
            onClick={() => router.back()}
            className="flex items-center justify-center bg-primary-200 rounded-full w-10 h-10 hover:bg-primary-300/20 transition-colors"
          >
            <Image src={icons.backArrow} alt="back" width={20} height={20} className="w-5 h-5" />
          </button>

          <h1 className="text-base font-rubik-medium text-black-300">
            Search for Your Ideal Home
          </h1>
          
          <div className="w-6 h-6">
            <Image src={icons.bell} alt="bell" width={24} height={24} className="w-6 h-6" />
          </div>
        </div>

        <Search />

        <div className="mt-8">
          <Filters />

          <div className="flex items-center justify-between mt-8 mb-4">
             <h2 className="text-xl font-rubik-bold text-black-300">
                Found {properties?.length || 0} Properties
             </h2>
          </div>

          {loading ? (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
              {Array.from({ length: 8 }).map((_, i) => (
                <div key={i} className="w-full h-72 bg-gray-100 animate-pulse rounded-xl" />
              ))}
            </div>
          ) : !properties || properties.length === 0 ? (
            <NoResults />
          ) : (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
              {properties.map((item) => (
                <Card
                  key={item.$id}
                  item={item}
                  onPress={() => handleCardPress(item.$id)}
                />
              ))}
            </div>
          )}
        </div>
      </main>
    </div>
  );
};

const Explore = () => {
    return (
        <Suspense fallback={<div>Loading...</div>}>
            <ExploreContent />
        </Suspense>
    )
}

export default Explore;
