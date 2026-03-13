import { Link, usePage } from '@inertiajs/react'
import { PropsWithChildren } from 'react'

type MenuItem = {
  id: number
  label: string
  url: string
  open_in_new_tab: boolean
}

function isExternalUrl(url: string): boolean {
  return /^https?:\/\//.test(url)
}

function MenuLink({ item, className }: { item: MenuItem; className?: string }) {
  if (isExternalUrl(item.url) || item.open_in_new_tab) {
    return (
      <a
        href={item.url}
        className={className}
        target={item.open_in_new_tab ? '_blank' : undefined}
        rel={item.open_in_new_tab ? 'noopener noreferrer' : undefined}
      >
        {item.label}
      </a>
    )
  }

  return (
    <Link href={item.url} className={className}>
      {item.label}
    </Link>
  )
}

export default function SiteLayout({
  children,
  menuItems = [],
}: PropsWithChildren<{ menuItems?: MenuItem[] }>) {
  const { url } = usePage()

  return (
    <div className="bg-base-100 min-h-screen">
      <div className="navbar border-base-300 bg-base-100 border-b px-0">
        <div className="container">
          <div className="flex w-full items-center">
            <div className="navbar-start">
              <div className="dropdown lg:hidden">
                <label tabIndex={0} className="btn btn-ghost" aria-label="Open menu">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    className="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      strokeWidth="2"
                      d="M4 6h16M4 12h16M4 18h16"
                    />
                  </svg>
                </label>

                <ul
                  tabIndex={0}
                  className="menu dropdown-content rounded-box bg-base-100 z-[1] mt-3 w-64 p-2 shadow"
                >
                  {menuItems.map((item) => (
                    <li key={item.id}>
                      <MenuLink item={item} className="link link-hover no-underline" />
                    </li>
                  ))}
                </ul>
              </div>

              <Link href="/" className="btn btn-ghost text-xl">
                Example
              </Link>
            </div>

            <div className="navbar-center hidden lg:flex">
              <ul className="menu menu-horizontal px-1">
                {menuItems.map((item) => {
                  const isActive = !isExternalUrl(item.url) && url === item.url

                  return (
                    <li key={item.id}>
                      <MenuLink
                        item={item}
                        className={`link link-hover no-underline ${isActive ? 'font-semibold' : ''}`}
                      />
                    </li>
                  )
                })}
              </ul>
            </div>

            <div className="navbar-end">
              <a href="/admin" className="btn btn-primary btn-sm">
                Admin
              </a>
            </div>
          </div>
        </div>
      </div>

      {children}

      <footer className="footer footer-center border-base-300 bg-base-200 text-base-content border-t p-8">
        <aside>
          <p>Built with Laravel, Filament, Inertia React, and daisyUI.</p>
        </aside>
      </footer>
    </div>
  )
}
