document.addEventListener('DOMContentLoaded', () => {
    // Reveal title on load
    const heroTitle = document.querySelector('.animate-title');
    if (heroTitle) {
        setTimeout(() => {
            heroTitle.classList.add('is-visible');
        }, 300);
    }

    // Keep your Image Tilt interactions (they look great on the spec sheet!)
    const images = document.querySelectorAll('.spec-img-wrapper img, .about-img, .collage-img');
    images.forEach(img => {
        img.addEventListener('mousemove', (e) => {
            const { offsetX, offsetY, target } = e;
            const { clientWidth, clientHeight } = target;
            const xPos = (offsetX / clientWidth) - 0.5;
            const yPos = (offsetY / clientHeight) - 0.5;
            target.style.transform = `perspective(1000px) rotateY(${xPos * 10}deg) rotateX(${-yPos * 10}deg) scale(1.05)`;
        });
        img.addEventListener('mouseleave', () => {
            img.style.transform = `perspective(1000px) rotateY(0deg) rotateX(0deg) scale(1)`;
        });
    });
});




gsap.registerPlugin(ScrollTrigger);

document.addEventListener('DOMContentLoaded', () => {

    const master = gsap.timeline({
        scrollTrigger: {
            trigger: "#gsap-scroll-area", // What section to watch
            start: "center center",      // Starts when the middle of the section hits the middle of screen
            end: "+=1500",               // This controls how MUCH the user has to scroll (higher = slower swap)
            pin: true,                   // This "freezes" it in place so the content doesn't fly by
            scrub: 1,                    // Links animation to mouse move
            markers: false               // No lines on screen
        }
    });

    // We cycle through all 4 pieces of content
    master.to(".s-pic1, .s-txt1", { opacity: 0, y: -20, duration: 1 });
    master.to(".s-pic2, .s-txt2", { opacity: 1, y: 0, duration: 1 }, "-=1");

    master.to(".s-pic2, .s-txt2", { opacity: 0, y: -20, duration: 1 });
    master.to(".s-pic3, .s-txt3", { opacity: 1, y: 0, duration: 1 }, "-=1");

    master.to(".s-pic3, .s-txt3", { opacity: 0, y: -20, duration: 1 });
    master.to(".s-pic4, .s-txt4", { opacity: 1, y: 0, duration: 1 }, "-=1");
});