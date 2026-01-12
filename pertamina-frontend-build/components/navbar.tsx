"use client"

import Link from "next/link"
import Image from "next/image"
import { usePathname } from "next/navigation"
import { useState, useEffect, useCallback } from "react"

export default function Navbar() {
  const pathname = usePathname()
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false)
  const [icons, setIcons] = useState<any>({})

  // Load icons dynamically to avoid HMR issues with Turbopack
  useEffect(() => {
    let isMounted = true;
    
    const loadIcons = async () => {
      try {
        const lucide = await import('lucide-react')
        if (isMounted) {
          setIcons({
            Menu: lucide.Menu,
            X: lucide.X
          })
        }
      } catch (error) {
        console.warn('Failed to load icons:', error)
        if (isMounted) {
          setIcons({})
        }
      }
    }
    
    loadIcons()
    
    // Cleanup function to prevent state updates on unmounted component
    return () => {
      isMounted = false;
    };
  }, [])

  // Lock body scroll when mobile menu is open
  useEffect(() => {
    if (mobileMenuOpen) {
      document.body.style.overflow = 'hidden'
    } else {
      document.body.style.overflow = 'unset'
    }
    return () => {
      document.body.style.overflow = 'unset'
    }
  }, [mobileMenuOpen])

  // Memoize the toggle function to prevent unnecessary re-renders
  const toggleMobileMenu = useCallback(() => {
    setMobileMenuOpen(prev => !prev)
  }, [])

  // Memoize the close function
  const closeMobileMenu = useCallback(() => {
    setMobileMenuOpen(false)
  }, [])

  const MenuIcon = icons.Menu
  const XIcon = icons.X

  return (
    <nav className="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-200/50 shadow-sm">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-16 relative">
          {/* Logo */}
          <Link href="/" className="flex items-center gap-3 active:scale-95 transition-transform" prefetch={true}>
            <Image
              src="/images/logo-pertamina-light.png"
              alt="Pertamina"
              width={160}
              height={140}
              className="h-[85px] md:h-[110px] w-auto object-contain"
              priority
              onError={(e) => {
                console.warn('Failed to load logo image:', e);
              }}
            />
          </Link>

          {/* Desktop Menu - Centered */}
          <div className="hidden md:flex items-center gap-8 absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2">
            {[
              { name: 'Home', path: '/' },
              { name: 'Maps', path: '/maps' },
              { name: 'Playlist', path: '/playlist' },
              { name: 'Contact', path: '/contact' },
            ].map((item) => {
              const isActive = item.path === '/' 
                ? pathname === '/' 
                : pathname?.startsWith(item.path);
              
              return (
                <Link 
                  key={item.path}
                  href={item.path} 
                  className={`relative py-1 text-sm font-bold transition-all duration-300 active:scale-95
                    ${isActive 
                      ? 'text-black opacity-100 scale-105' 
                      : 'text-black opacity-30 hover:opacity-100'
                    }
                  `}
                  prefetch={true}
                >
                  {item.name}
                  {isActive && (
                    <span className="absolute -bottom-1 left-0 w-full h-[3px] bg-black rounded-full" />
                  )}
                </Link>
              )
            })}
          </div>

          {/* Mobile Menu Button - Right Aligned */}
          <button 
            className="md:hidden p-2 text-gray-800 hover:bg-gray-100 rounded-lg transition-colors" 
            onClick={toggleMobileMenu}
            aria-label={mobileMenuOpen ? "Close menu" : "Open menu"}
          >
            {mobileMenuOpen ? (
              XIcon ? <XIcon size={28} /> : <div className="w-7 h-7" />
            ) : (
              MenuIcon ? <MenuIcon size={28} /> : <div className="w-7 h-7" />
            )}
          </button>
        </div>
      </div>
      
      {/* Mobile Sidebar Overlay & Container */}
      {mobileMenuOpen && (
        <>
          {/* Backdrop Overlay */}
          <div 
            className="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 md:hidden animate-in fade-in duration-300"
            onClick={closeMobileMenu}
          />
          
          {/* Sidebar Panel */}
          <div className="fixed top-0 right-0 h-screen w-72 bg-white z-50 md:hidden shadow-2xl animate-in slide-in-from-right duration-300 ease-out flex flex-col">
            {/* Close Button Inside Sidebar */}
            <button 
              className="absolute top-4 right-4 p-2 text-gray-400 hover:text-black hover:bg-gray-100 rounded-full transition-all duration-300 active:scale-90"
              onClick={closeMobileMenu}
              aria-label="Close menu"
            >
              {XIcon ? <XIcon size={24} /> : <div className="w-6 h-6" />}
            </button>

            {/* Sidebar Header - Logo Top & Centered */}
            <div className="flex justify-center px-6 pt-0 pb-0">
              <Link href="/" className="flex items-center -mt-4 active:scale-95 transition-transform" prefetch={true} onClick={closeMobileMenu}>
                <Image
                  src="/images/logo-pertamina-light.png"
                  alt="Pertamina"
                  width={340}
                  height={220}
                  className="h-40 md:h-44 w-auto object-contain"
                  priority
                />
              </Link>
            </div>

            {/* Navigation Links - Brought Closer to Logo */}
            <div className="flex-1 py-0 mt-0 overflow-y-auto">
              <div className="flex flex-col px-4 gap-2">
                {[
                  { name: 'Home', path: '/' },
                  { name: 'Maps', path: '/maps' },
                  { name: 'Playlist', path: '/playlist' },
                  { name: 'Contact', path: '/contact' },
                ].map((item) => {
                  const isActive = item.path === '/' 
                    ? pathname === '/' 
                    : pathname?.startsWith(item.path);

                  return (
                    <Link 
                      key={item.path}
                      href={item.path} 
                      className={`flex items-center justify-between px-6 py-4 rounded-xl text-base font-bold transition-all duration-300 active:scale-95 ${
                        isActive 
                          ? 'bg-gray-50 text-black opacity-100' 
                          : 'text-black opacity-30 hover:opacity-100 hover:bg-gray-50/50'
                      }`}
                      onClick={closeMobileMenu}
                      prefetch={true}
                    >
                      <span>{item.name}</span>
                      {isActive && <div className="w-2.5 h-2.5 bg-black rounded-full" />}
                    </Link>
                  )
                })}
              </div>
            </div>

          </div>
        </>
      )}
    </nav>
  )
}