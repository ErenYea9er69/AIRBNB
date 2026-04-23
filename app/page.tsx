"use client";

import React, { useEffect } from "react";
import Image from "next/image";
import { useRouter, useSearchParams } from "next/navigation";
import { useGlobalContext } from "@/lib/global-provider";
import { useAppwrite } from "@/lib/useAppwrite";
import { getLatestProperties, getProperties } from "@/lib/appwrite";

import icons from "@/constants/icons";
import Search from "@/components/Search";
import Filters from "@/components/Filters";
import NoResults from "@/components/NoResults";
import { Card, FeaturedCard } from "@/components/Cards";
import Navbar from "@/components/Navbar";
import { Suspense } from "react";

const HomeContent = () => {
  const { user, loading: userLoading, isLogged } = useGlobalContext();
  const searchParams = useSearchParams();
  const router = useRouter();

  const query = searchParams.get("query") || "";
  const filter = searchParams.get("filter") || "All";

  const { data: latestProperties, loading: latestPropertiesLoading } =
    useAppwrite({
      fn: getLatestProperties,
    });

  const {
    data: properties,
    refetch,
    loading: propertiesLoading,
  } = useAppwrite({
    fn: getProperties,
    params: {
      filter: filter,
      query: query,
      limit: 6,
    },
    skip: true,
  });

  useEffect(() => {
    refetch({
      filter: filter,
      query: query,
      limit: 6,
    });
  }, [filter, query]);

  useEffect(() => {
    if (!userLoading && !isLogged) {
      router.push("/sign-in");
    }
  }, [userLoading, isLogged, router]);

  const handleCardPress = (id: string) => router.push(`/properties/${id}`);

  if (userLoading) return null;

  return (
    <div className="min-h-screen bg-white pb-24 md:pb-0 md:pt-20">
      <Navbar />
      
      <main className="max-w-7xl mx-auto px-6 py-10">
        {/* User Greeting Section */}
        <div className="flex flex-row items-center justify-between">
          <div className="flex flex-row items-center">
            {user?.avatar && (
              <div className="relative w-12 h-12 rounded-full overflow-hidden border-2 border-primary-100">
                <Image
                  src={user.avatar}
                  alt={user.name}
                  fill
                  className="object-cover"
                />
              </div>
            )}

            <div className="flex flex-col items-start ml-3">
              <p className="text-xs font-rubik text-black-100">
                Good Morning
              </p>
              <h2 className="text-base font-rubik-medium text-black-300">
                {user?.name}
              </h2>
            </div>
          </div>
          <button className="p-2 hover:bg-gray-100 rounded-full transition-colors relative">
            <Image src={icons.bell} alt="bell" width={24} height={24} className="w-6 h-6" />
            <div className="absolute top-2 right-2 w-2 h-2 bg-danger rounded-full border border-white" />
          </button>
        </div>

        <Search />

        {/* Featured Section */}
        <section className="mt-10">
          <div className="flex flex-row items-center justify-between">
            <h2 className="text-xl font-rubik-bold text-black-300">
              Featured
            </h2>
            <button className="text-base font-rubik-bold text-primary-300 hover:text-primary-200 transition-colors">
              See all
            </button>
          </div>

          <div className="flex flex-row gap-5 overflow-x-auto no-scrollbar mt-5 py-2">
            {latestPropertiesLoading ? (
              Array.from({ length: 3 }).map((_, i) => (
                <div key={i} className="w-64 h-96 bg-gray-100 animate-pulse rounded-2xl shrink-0" />
              ))
            ) : !latestProperties || latestProperties.length === 0 ? (
              <NoResults />
            ) : (
              latestProperties.map((item) => (
                <FeaturedCard
                  key={item.$id}
                  item={item}
                  onPress={() => handleCardPress(item.$id)}
                />
              ))
            )}
          </div>
        </section>

        {/* Recommendations Section */}
        <section className="mt-10">
          <div className="flex flex-row items-center justify-between">
            <h2 className="text-xl font-rubik-bold text-black-300">
              Our Recommendation
            </h2>
            <button className="text-base font-rubik-bold text-primary-300 hover:text-primary-200 transition-colors">
              See all
            </button>
          </div>

          <Filters />

          {propertiesLoading ? (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-5">
              {Array.from({ length: 6 }).map((_, i) => (
                <div key={i} className="w-full h-72 bg-gray-100 animate-pulse rounded-xl" />
              ))}
            </div>
          ) : !properties || properties.length === 0 ? (
            <NoResults />
          ) : (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-5">
              {properties.map((item) => (
                <Card
                  key={item.$id}
                  item={item}
                  onPress={() => handleCardPress(item.$id)}
                />
              ))}
            </div>
          )}
        </section>
      </main>
    </div>
  );
};

const Home = () => {
  return (
    <Suspense fallback={<div>Loading...</div>}>
      <HomeContent />
    </Suspense>
  );
};

export default Home;
