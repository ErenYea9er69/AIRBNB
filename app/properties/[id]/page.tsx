"use client";

import React from "react";
import Image from "next/image";
import { useRouter } from "next/navigation";
import { useAppwrite } from "@/lib/useAppwrite";
import { getPropertyById } from "@/lib/appwrite";
import icons from "@/constants/icons";
import images from "@/constants/images";
import Comment from "@/components/Comment";
import { facilities as facilityList } from "@/constants/data";
import Navbar from "@/components/Navbar";

interface PageProps {
  params: { id: string };
}

const Property = ({ params }: PageProps) => {
  const { id } = params;
  const router = useRouter();

  const { data: propertyData, loading } = useAppwrite({
    fn: getPropertyById,
    params: {
      id: id,
    },
  });

  const property = propertyData as any;

  if (loading) {
    return (
      <div className="min-h-screen bg-white flex items-center justify-center">
        <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-primary-300"></div>
      </div>
    );
  }

  if (!property) return null;

  return (
    <div className="min-h-screen bg-white pb-32 md:pt-20">
      <Navbar />

      <main className="max-w-7xl mx-auto md:px-6">
        {/* Hero Section / Image */}
        <div className="relative w-full h-[50vh] md:h-[60vh] md:rounded-3xl overflow-hidden shadow-2xl">
          {property.image && (
            <Image
              src={property.image}
              alt={property.name}
              fill
              className="object-cover"
              priority
            />
          )}
          <Image
            src={images.whiteGradient}
            alt="gradient"
            fill
            className="absolute top-0 w-full z-10 opacity-30 pointer-events-none"
          />

          <div className="z-20 absolute top-6 left-6 md:top-10 md:left-10 right-6 md:right-10 flex flex-row items-center justify-between">
            <button
              onClick={() => router.back()}
              className="bg-white/80 backdrop-blur-md rounded-full w-12 h-12 flex items-center justify-center hover:bg-white transition-colors shadow-lg"
            >
              <Image src={icons.backArrow} alt="back" width={24} height={24} className="w-6 h-6" />
            </button>

            <div className="flex flex-row items-center gap-4">
              <button className="bg-white/80 backdrop-blur-md rounded-full w-12 h-12 flex items-center justify-center hover:bg-white transition-colors shadow-lg group">
                <Image
                  src={icons.heart}
                  alt="fav"
                  width={24}
                  height={24}
                  className="w-6 h-6 transition-transform group-hover:scale-110"
                />
              </button>
              <button className="bg-white/80 backdrop-blur-md rounded-full w-12 h-12 flex items-center justify-center hover:bg-white transition-colors shadow-lg">
                <Image src={icons.send} alt="share" width={24} height={24} className="w-6 h-6" />
              </button>
            </div>
          </div>
        </div>

        <div className="px-6 md:px-0 mt-10 grid grid-cols-1 lg:grid-cols-3 gap-12">
          {/* Main Info */}
          <div className="lg:col-span-2 flex flex-col gap-8">
            <div>
              <h1 className="text-3xl md:text-4xl font-rubik-extrabold text-black-300">
                {property.name}
              </h1>

              <div className="flex flex-wrap items-center gap-4 mt-4">
                <div className="px-4 py-2 bg-primary-100 rounded-full">
                  <span className="text-sm font-rubik-bold text-primary-300">
                    {property.type}
                  </span>
                </div>

                <div className="flex flex-row items-center gap-2">
                  <Image src={icons.star} alt="star" width={20} height={20} className="w-5 h-5" />
                  <span className="text-black-200 text-base font-rubik-medium">
                    {property.rating} ({property.reviews?.length || 0} reviews)
                  </span>
                </div>
              </div>

              <div className="flex flex-row items-center gap-8 mt-8 border-y border-gray-100 py-6">
                {[
                  { icon: icons.bed, label: "Beds", value: property.bedrooms },
                  { icon: icons.bath, label: "Baths", value: property.bathrooms },
                  { icon: icons.area, label: "sqft", value: property.area },
                ].map((stat, i) => (
                  <div key={i} className="flex flex-row items-center">
                    <div className="flex items-center justify-center bg-primary-100 rounded-full w-10 h-10">
                      <Image src={stat.icon} alt={stat.label} width={16} height={16} className="w-4 h-4" />
                    </div>
                    <span className="text-black-300 text-base font-rubik-medium ml-3">
                      {stat.value} {stat.label}
                    </span>
                  </div>
                ))}
              </div>
            </div>

            {/* Overview */}
            <div className="flex flex-col gap-3">
              <h2 className="text-black-300 text-2xl font-rubik-bold">Overview</h2>
              <p className="text-black-200 text-lg font-rubik leading-relaxed">
                {property.description}
              </p>
            </div>

            {/* Facilities */}
            <div className="flex flex-col gap-4">
              <h2 className="text-black-300 text-2xl font-rubik-bold">Facilities</h2>
              <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
                {property.facilities?.map((item: string, index: number) => {
                  const facility = facilityList.find(
                    (f) => f.title === item
                  );
                  return (
                    <div key={index} className="flex flex-col items-center gap-2 p-4 bg-gray-50 rounded-2xl hover:bg-white hover:shadow-md transition-all">
                      <div className="w-14 h-14 bg-primary-100 rounded-full flex items-center justify-center">
                        <Image
                          src={facility ? facility.icon : icons.info}
                          alt={item}
                          width={24}
                          height={24}
                          className="w-6 h-6"
                        />
                      </div>
                      <span className="text-black-300 text-sm text-center font-rubik font-medium">
                        {item}
                      </span>
                    </div>
                  );
                })}
              </div>
            </div>

            {/* Gallery */}
            {property.gallery?.length > 0 && (
              <div className="flex flex-col gap-4">
                <h2 className="text-black-300 text-2xl font-rubik-bold">Gallery</h2>
                <div className="flex flex-row gap-4 overflow-x-auto no-scrollbar pb-2">
                  {property.gallery.map((item: any) => (
                    <div key={item.$id} className="relative w-64 h-44 rounded-2xl overflow-hidden shadow-md shrink-0">
                      <Image
                        src={item.image}
                        alt="gallery"
                        fill
                        className="object-cover hover:scale-110 transition-transform duration-500"
                      />
                    </div>
                  ))}
                </div>
              </div>
            )}
          </div>

          {/* Sidebar / Location & Reviews */}
          <div className="flex flex-col gap-8 lg:sticky lg:top-32 h-fit">
            <div className="bg-white p-6 rounded-3xl border border-gray-100 shadow-xl">
               <h2 className="text-black-300 text-xl font-rubik-bold">Agent</h2>
               <div className="flex items-center mt-6">
                 {property.agent?.avatar && (
                   <div className="relative w-16 h-16 rounded-full overflow-hidden border-2 border-primary-100">
                      <Image src={property.agent.avatar} alt={property.agent.name} fill className="object-cover" />
                   </div>
                 )}
                 <div className="ml-4 flex-1">
                    <h3 className="text-lg font-rubik-bold text-black-300">{property.agent?.name}</h3>
                    <p className="text-sm text-black-200">{property.agent?.email}</p>
                 </div>
                 <div className="flex gap-2">
                    <button className="p-2 bg-primary-100 rounded-full hover:bg-primary-200 transition-colors">
                        <Image src={icons.chat} alt="chat" width={20} height={20} className="w-5 h-5" />
                    </button>
                    <button className="p-2 bg-primary-100 rounded-full hover:bg-primary-200 transition-colors">
                        <Image src={icons.phone} alt="phone" width={20} height={20} className="w-5 h-5" />
                    </button>
                 </div>
               </div>
            </div>

            <div className="bg-white p-6 rounded-3xl border border-gray-100 shadow-xl">
               <h2 className="text-black-300 text-xl font-rubik-bold mb-4">Location</h2>
               <div className="flex items-start gap-2 mb-4">
                  <Image src={icons.location} alt="location" width={20} height={20} className="w-5 h-5 mt-1" />
                  <p className="text-black-200 text-sm font-rubik leading-relaxed">
                    {property.address}
                  </p>
               </div>
               <div className="relative w-full h-48 rounded-2xl overflow-hidden">
                  <Image src={images.map} alt="map" fill className="object-cover" />
               </div>
            </div>

            {property.reviews?.length > 0 && (
                <div className="flex flex-col gap-4">
                    <div className="flex items-center justify-between">
                         <h2 className="text-black-300 text-xl font-rubik-bold">Reviews</h2>
                         <button className="text-primary-300 font-rubik-bold text-sm">View All</button>
                    </div>
                    <Comment item={property.reviews[0]} />
                </div>
            )}
          </div>
        </div>
      </main>

      {/* Booking Bar (Fixed Mobile, Relative/Sticky MD+) */}
      <div className="fixed md:static -bottom-1 left-0 right-0 bg-white/80 backdrop-blur-xl border-t border-gray-100 p-6 z-50 md:mt-12 md:bg-white">
        <div className="max-w-7xl mx-auto flex flex-row items-center justify-between gap-10">
          <div className="flex flex-col items-start">
            <span className="text-black-200 text-xs font-rubik-medium uppercase tracking-wider">
              Total Price
            </span>
            <span className="text-primary-300 text-3xl font-rubik-extrabold">
              ${property.price}
            </span>
          </div>

          <button className="flex-1 max-w-md bg-primary-300 py-4 rounded-full shadow-lg shadow-primary-300/30 hover:bg-primary-300 hover:-translate-y-1 transition-all duration-300 text-white text-lg font-rubik-bold">
            Book Appointment
          </button>
        </div>
      </div>
    </div>
  );
};

export default Property;
