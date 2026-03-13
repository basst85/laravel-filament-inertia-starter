type Slide = {
  id: number
  image_url: string
  caption?: string | null
  alt_text?: string | null
}

export default function PageSlider({ slides }: { slides: Slide[] }) {
  if (!slides.length) {
    return null
  }

  return (
    <section className="mb-10">
      <div className="carousel rounded-box w-full shadow-xl">
        {slides.map((slide, index) => {
          const prev = index === 0 ? slides.length - 1 : index - 1
          const next = index === slides.length - 1 ? 0 : index + 1

          return (
            <div key={slide.id} id={`slide-${index}`} className="carousel-item relative w-full">
              <img
                src={slide.image_url}
                alt={slide.alt_text || ''}
                className="h-[420px] w-full object-cover"
              />

              <div className="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-6 text-white">
                {slide.caption && <p className="text-lg font-medium">{slide.caption}</p>}
              </div>

              <div className="absolute top-1/2 right-5 left-5 flex -translate-y-1/2 justify-between">
                <a href={`#slide-${prev}`} className="btn btn-circle">
                  ❮
                </a>
                <a href={`#slide-${next}`} className="btn btn-circle">
                  ❯
                </a>
              </div>
            </div>
          )
        })}
      </div>
    </section>
  )
}
