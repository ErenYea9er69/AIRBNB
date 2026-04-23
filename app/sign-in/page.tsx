"use client";

import React, { useEffect, useState } from "react";
import Image from "next/image";
import { useRouter } from "next/navigation";
import { loginWithEmail, register } from "@/lib/appwrite";
import { useGlobalContext } from "@/lib/global-provider";
import images from "@/constants/images";

const SignIn = () => {
  const { refetch, loading, isLogged } = useGlobalContext();
  const router = useRouter();

  const [isRegister, setIsRegister] = useState(false);
  const [formData, setFormData] = useState({
    name: "",
    subname: "",
    email: "",
    password: "",
  });
  const [error, setError] = useState("");
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    if (!loading && isLogged) {
      router.push("/");
    }
  }, [loading, isLogged, router]);

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");
    setSubmitting(true);

    try {
      let result;
      if (isRegister) {
        const full_name = `${formData.name} ${formData.subname}`.trim();
        result = await register({
          email: formData.email,
          password: formData.password,
          name: full_name,
        });
      } else {
        result = await loginWithEmail({
          email: formData.email,
          password: formData.password,
        });
      }

      if (result) {
        refetch();
      } else {
        setError(isRegister ? "Registration failed" : "Login failed. Please check your credentials.");
      }
    } catch (err: any) {
      setError(err.message || "An unexpected error occurred");
    } finally {
      setSubmitting(false);
    }
  };

  if (loading) return null;

  return (
    <div className="min-h-screen bg-white flex flex-col items-center justify-start overflow-y-auto pb-10">
      <div className="w-full max-w-[500px] flex flex-col items-center">
        {/* Onboarding Image */}
        <div className="relative w-full aspect-[3/4] md:aspect-square mt-4">
          <Image
            src={images.onboarding}
            alt="Onboarding"
            fill
            className="object-contain"
            priority
          />
        </div>

        <div className="px-10 mt-6 w-full flex flex-col items-center">
          <p className="text-sm uppercase font-rubik text-black-200 tracking-widest">
            Welcome To Real Scout
          </p>

          <h1 className="text-2xl font-rubik-bold text-black-300 text-center mt-2 leading-tight">
            Let's Get You Closer To <br />
            <span className="text-primary-300">Your Ideal Home</span>
          </h1>

          <form onSubmit={handleSubmit} className="w-full mt-8 flex flex-col gap-4">
            {isRegister && (
              <div className="flex gap-4">
                <div className="flex-1 flex flex-col gap-1">
                    <label className="text-xs font-rubik-medium text-black-200 ml-1">Name</label>
                    <input
                        type="text"
                        name="name"
                        value={formData.name}
                        onChange={handleInputChange}
                        required
                        className="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 outline-none focus:border-primary-300 transition-colors font-rubik text-sm"
                        placeholder="First Name"
                    />
                </div>
                <div className="flex-1 flex flex-col gap-1">
                    <label className="text-xs font-rubik-medium text-black-200 ml-1">Subname</label>
                    <input
                        type="text"
                        name="subname"
                        value={formData.subname}
                        onChange={handleInputChange}
                        required
                        className="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 outline-none focus:border-primary-300 transition-colors font-rubik text-sm"
                        placeholder="Last Name"
                    />
                </div>
              </div>
            )}

            <div className="flex flex-col gap-1">
              <label className="text-xs font-rubik-medium text-black-200 ml-1">Email</label>
              <input
                type="email"
                name="email"
                value={formData.email}
                onChange={handleInputChange}
                required
                className="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 outline-none focus:border-primary-300 transition-colors font-rubik text-sm"
                placeholder="example@mail.com"
              />
            </div>

            <div className="flex flex-col gap-1">
              <label className="text-xs font-rubik-medium text-black-200 ml-1">Password</label>
              <input
                type="password"
                name="password"
                value={formData.password}
                onChange={handleInputChange}
                required
                minLength={8}
                className="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 outline-none focus:border-primary-300 transition-colors font-rubik text-sm"
                placeholder="••••••••"
              />
            </div>

            {error && (
              <p className="text-danger text-xs font-rubik-medium mt-1 text-center">
                {error}
              </p>
            )}

            <button
              type="submit"
              disabled={submitting}
              className={`bg-primary-300 rounded-full w-full py-4 mt-4 shadow-lg shadow-primary-300/20 hover:bg-primary-300/90 transition-all duration-300 text-white font-rubik-bold text-lg ${submitting ? "opacity-50 cursor-not-allowed" : ""}`}
            >
              {submitting ? "Processing..." : isRegister ? "Create Account" : "Sign In"}
            </button>
          </form>

          <div className="mt-8 flex flex-row items-center gap-2">
            <p className="text-sm font-rubik text-black-100">
              {isRegister ? "Already have an account?" : "Don't have an account?"}
            </p>
            <button
              onClick={() => setIsRegister(!isRegister)}
              className="text-sm font-rubik-bold text-primary-300 hover:text-primary-300/80 transition-colors"
            >
              {isRegister ? "Sign In" : "Sign Up"}
            </button>
          </div>
          
          <p className="text-[10px] text-center text-black-100 mt-10 opacity-60">
            By continuing, you agree to our Terms of Service and Privacy Policy.
          </p>
        </div>
      </div>
    </div>
  );
};

export default SignIn;
