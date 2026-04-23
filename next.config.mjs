/** @type {import('next').NextConfig} */
const nextConfig = {
  images: {
    remotePatterns: [
      {
        protocol: 'https',
        hostname: 'cloud.appwrite.io',
      },
      {
        protocol: 'https',
        hostname: 'images.unsplash.com',
      },
       {
        protocol: 'https',
        hostname: 'unsplash.com',
      },
    ],
  },
};

export default nextConfig;
