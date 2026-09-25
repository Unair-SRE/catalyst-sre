const homepage = document.querySelector('.home-page');

if (homepage) {
    const revealItems = [...homepage.querySelectorAll('[data-reveal]')];
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (!reduceMotion && 'IntersectionObserver' in window && revealItems.length > 0) {
        const reveal = (element) => element.classList.add('is-revealed');

        revealItems.forEach((element) => {
            if (element.getBoundingClientRect().top < window.innerHeight * 0.94) {
                reveal(element);
            }
        });

        homepage.dataset.revealReady = 'true';

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                reveal(entry.target);
                observer.unobserve(entry.target);
            });
        }, {
            rootMargin: '0px 0px -8% 0px',
            threshold: 0.12,
        });

        revealItems
            .filter((element) => !element.classList.contains('is-revealed'))
            .forEach((element) => observer.observe(element));
    }
}
