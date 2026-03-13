import { Head } from '@inertiajs/react'
import PageSlider from '@/components/page-slider'
import SiteLayout from '@/components/site-layout'

type Slide = {
  id: number
  image_url: string
  caption?: string | null
  alt_text?: string | null
}

type Page = {
  id: number
  title: string
  description?: string | null
  slug: string
  hero?: {
    title?: string | null
    text?: string | null
    image_url?: string | null
    button_label?: string | null
    button_url?: string | null
  }
  content?: string | null
  slides: Slide[]
}

type MenuItem = {
  id: number
  label: string
  url: string
  open_in_new_tab: boolean
}

export default function Show({
  page,
  menuItems,
  fallbackText,
}: {
  page: Page
  menuItems: MenuItem[]
  fallbackText?: string
}) {
  const metaDescription = page.description || page.hero?.text || undefined

  const hasHero = Boolean(
    page.hero?.title ||
    page.hero?.text ||
    page.hero?.image_url ||
    (page.hero?.button_label && page.hero?.button_url),
  )

  return (
    <SiteLayout menuItems={menuItems}>
      <Head title={page.title}>
        {metaDescription && (
          <meta head-key="description" name="description" content={metaDescription} />
        )}
      </Head>

      <main className="container py-10">
        {fallbackText && (
          <div className="alert alert-warning mb-6">
            <span>{fallbackText}</span>
          </div>
        )}

        {hasHero ? (
          <section
            className="hero rounded-box mb-8 min-h-[22rem] overflow-hidden"
            style={
              page.hero?.image_url
                ? {
                    backgroundImage: `url(${page.hero.image_url})`,
                    backgroundPosition: 'center',
                    backgroundSize: 'cover',
                  }
                : undefined
            }
          >
            <div className="hero-overlay bg-black/55" />
            <div className="hero-content text-neutral-content text-center">
              <div className="max-w-2xl">
                <h1 className="text-4xl font-bold md:text-5xl">{page.hero?.title || page.title}</h1>

                {(page.hero?.text || page.description) && (
                  <p className="py-4 text-lg opacity-95">{page.hero?.text || page.description}</p>
                )}

                {page.hero?.button_label && page.hero?.button_url && (
                  <a href={page.hero.button_url} className="btn btn-primary">
                    {page.hero.button_label}
                  </a>
                )}
              </div>
            </div>
          </section>
        ) : (
          <div className="hero rounded-box bg-base-200 mb-8">
            <div className="hero-content w-full justify-start px-8 py-12">
              <div className="max-w-3xl">
                <h1 className="text-4xl font-bold md:text-5xl">{page.title}</h1>

                {page.description && <p className="mt-4 text-lg opacity-80">{page.description}</p>}
              </div>
            </div>
          </div>
        )}

        <PageSlider slides={page.slides} />

        <article
          className="prose prose-lg max-w-none"
          dangerouslySetInnerHTML={{ __html: page.content || '' }}
        />
      </main>
    </SiteLayout>
  )
}
