"use client";
import React from "react";
import { motion } from "motion/react";
import "./testimonials.css";

export interface TestimonialItem {
  text: string;
  image: string;
  name: string;
  role: string;
}

export const testimonials: TestimonialItem[] = [
  {
    text: "Pre-orders on MarketLink let us harvest exactly what our customers need at sunrise on Saturday. We have zero crop spoilage and our stall at Downtown Plaza runs smoothly every single weekend.",
    image: "/images/farmers/farmer-2.jpg",
    name: "Johnathan Green",
    role: "Lead Grower, Green Valley Organic Produce",
  },
  {
    text: "Reserving my heirloom tomatoes and fresh sourdough on Thursday night means I never arrive to empty baskets. Pickup at Johnathan's stall takes under two minutes!",
    image: "https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=160&q=80",
    name: "Sarah Shopper",
    role: "Verified Customer & Weekend Regular",
  },
  {
    text: "As an independent fruit and honey producer, knowing our weekend pre-orders beforehand lets us pack and label our raw wildflower honey jars with zero morning rush.",
    image: "/images/farmers/farmer-3.jpg",
    name: "Elena Rodriguez",
    role: "Orchardist, Sunshine Orchards & Apiary",
  },
  {
    text: "Direct community feedback and guaranteed stall pickups have completely changed our microgreens business. MarketLink gives family growers the technology to thrive.",
    image: "/images/farmers/farmer-1.webp",
    name: "Marcus Vance",
    role: "Grower, New Harvest Urban Greens",
  },
  {
    text: "Customers reserve our rare culinary herbs and medicinal teas during the week and pick them up at Saturday market. It's direct, personal, and transparent.",
    image: "/images/farmers/farmer-5.jpg",
    name: "Amina Chen",
    role: "Master Herbalist, Silk Road Herbs & Honey",
  },
  {
    text: "Baking naturally fermented sourdough takes 24 hours. Having customer pre-orders confirmed on MarketLink helps us mill and bake the exact right batch every Friday.",
    image: "/images/farmers/farmer-10.jpg",
    name: "Sarah Jenkins",
    role: "Head Baker, Artisan Hearth Bakery & Mill",
  },
  {
    text: "Our artisanal goat cheeses and fresh raw milk bottles sell out quickly. With MarketLink, regular customers never miss out on their weekly staple pantry goods.",
    image: "/images/farmers/farmer-2.jpg",
    name: "Liam MacIntyre",
    role: "Cheesemaker, Pasture Gold Creamery",
  },
  {
    text: "Direct farmer-to-consumer pre-orders mean no middlemen cutting into our margins and customers receiving tree-ripened Valencia oranges picked hours prior.",
    image: "/images/farmers/farmer-4.webp",
    name: "Mateo Silva",
    role: "Citrus Grower, Sunset Bay Citrus & Berries",
  },
  {
    text: "Finding out which farmers are attending Wednesday and Saturday markets with interactive maps makes meal planning for my family effortless and joyful.",
    image: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=160&q=80",
    name: "David Miller",
    role: "Verified MarketLink Shopper",
  },
];

export const TestimonialsColumn = (props: {
  className?: string;
  testimonials: TestimonialItem[];
  duration?: number;
}) => {
  return (
    <div className={props.className}>
      <motion.div
        animate={{
          translateY: "-50%",
        }}
        transition={{
          duration: props.duration || 10,
          repeat: Infinity,
          ease: "linear",
          repeatType: "loop",
        }}
        className="testimonials-col-track flex flex-col gap-6 pb-6 bg-transparent"
      >
        {[
          ...new Array(2).fill(0).map((_, index) => (
            <React.Fragment key={index}>
              {props.testimonials.map(({ text, image, name, role }, i) => (
                <div
                  className="testimonial-card p-8 sm:p-10 rounded-3xl border border-stone-200/80 bg-white/95 max-w-xs w-full transition-all"
                  key={i}
                >
                  <p className="testimonial-quote text-stone-700 text-sm leading-relaxed font-normal">
                    &ldquo;{text}&rdquo;
                  </p>
                  <div className="testimonial-author flex items-center gap-3 mt-5 pt-3">
                    <img
                      width={44}
                      height={44}
                      src={image}
                      alt={name}
                      className="testimonial-avatar h-10 w-10 rounded-full object-cover"
                      loading="lazy"
                    />
                    <div className="flex flex-col">
                      <div className="testimonial-author-name font-semibold text-stone-900 tracking-tight leading-5 text-sm">
                        {name}
                      </div>
                      <div className="testimonial-author-role text-emerald-700 text-xs font-medium">
                        {role}
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </React.Fragment>
          )),
        ]}
      </motion.div>
    </div>
  );
};

const firstColumn = testimonials.slice(0, 3);
const secondColumn = testimonials.slice(3, 6);
const thirdColumn = testimonials.slice(6, 9);

export const Testimonials = () => {
  return (
    <section className="testimonials-section bg-gradient-to-b from-stone-50/60 to-white relative overflow-hidden">
      <div className="testimonials-container container z-10 mx-auto px-4">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 0.1, ease: [0.16, 1, 0.3, 1] }}
          viewport={{ once: true }}
          className="testimonials-header flex flex-col items-center justify-center max-w-[620px] mx-auto text-center"
        >
          <div className="flex justify-center">
            <span className="testimonials-badge inline-flex items-center gap-1.5 py-1 px-3.5 rounded-full text-xs font-semibold tracking-wide uppercase">
              <svg className="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24" width="14" height="14">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
              </svg>
              Community Voices
            </span>
          </div>

          <h2 className="testimonials-title text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold tracking-tight mt-4 text-stone-900 heading-serif">
            What our community says
          </h2>
          <p className="testimonials-subtitle text-center mt-3 text-stone-600 text-sm sm:text-base leading-relaxed">
            Real stories from local shoppers, restaurant chefs, and independent growers sharing their MarketLink market day experience.
          </p>
        </motion.div>

        <div className="testimonials-columns-wrapper flex justify-center gap-6 mt-12 overflow-hidden">
          <TestimonialsColumn testimonials={firstColumn} className="testimonials-col" duration={18} />
          <TestimonialsColumn
            testimonials={secondColumn}
            className="testimonials-col testimonials-col-2 hidden md:block"
            duration={22}
          />
          <TestimonialsColumn
            testimonials={thirdColumn}
            className="testimonials-col testimonials-col-3 hidden lg:block"
            duration={19}
          />
        </div>
      </div>
    </section>
  );
};

export default { Testimonials };
