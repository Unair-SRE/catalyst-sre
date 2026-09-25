import './dashboard-countdown';
import './public-navbar';
import './verification-countdown';

document.addEventListener("DOMContentLoaded", () => {

    const nextSection = document.querySelector(".next-section");

    console.log("NEXT SECTION:", nextSection);

    if (!nextSection) return;


    const observer = new IntersectionObserver(
        (entries) => {

            entries.forEach((entry) => {

                if (entry.isIntersecting) {

                    console.log("NEXT SHOW");

                    nextSection.classList.add("show");

                    observer.unobserve(entry.target);
                }

            });

        },
        {
            threshold: 0.2
        }
    );


    observer.observe(nextSection);

});
