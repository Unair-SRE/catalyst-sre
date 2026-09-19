const navbar = document.querySelector('[data-public-navbar]');

if (navbar) {
    const surface = navbar.querySelector('.public-navbar__surface');
    const menu = navbar.querySelector('[data-public-navbar-menu]');
    const menuToggle = navbar.querySelector('[data-public-navbar-toggle]');
    let observer;
    let scrollFrame;
    let resizeFrame;

    const updateScrolledState = () => {
        navbar.dataset.scrolled = window.scrollY > 12 ? 'true' : 'false';
        scrollFrame = undefined;
    };

    const requestScrollUpdate = () => {
        if (scrollFrame) return;
        scrollFrame = requestAnimationFrame(updateScrolledState);
    };

    const buildThemeObserver = () => {
        observer?.disconnect();
        navbar.dataset.foreground = 'dark';

        const themedSections = [...document.querySelectorAll('[data-navbar-theme]')];
        if (!themedSections.length || !surface) return;

        const surfaceRect = surface.getBoundingClientRect();
        const probeY = Math.max(0, Math.min(window.innerHeight - 1, Math.round(surfaceRect.top + surfaceRect.height / 2)));
        const bottomMargin = Math.max(0, window.innerHeight - probeY - 1);
        const intersecting = new Map();

        observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) intersecting.set(entry.target, entry.boundingClientRect.top);
                else intersecting.delete(entry.target);
            });

            const activeSection = [...intersecting.entries()]
                .sort((first, second) => Math.abs(first[1] - probeY) - Math.abs(second[1] - probeY))[0]?.[0];
            const requestedTheme = activeSection?.dataset.navbarTheme;
            navbar.dataset.foreground = requestedTheme === 'light' ? 'light' : 'dark';
        }, {
            rootMargin: `-${probeY}px 0px -${bottomMargin}px 0px`,
            threshold: 0,
        });

        themedSections.forEach(section => observer.observe(section));
    };

    const scheduleThemeObserver = () => {
        if (resizeFrame) cancelAnimationFrame(resizeFrame);
        resizeFrame = requestAnimationFrame(buildThemeObserver);
    };

    const updateMenuState = () => {
        const isOpen = Boolean(menu?.open);
        navbar.dataset.menuOpen = isOpen ? 'true' : 'false';
        menuToggle?.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        menuToggle?.setAttribute('aria-label', isOpen ? 'Close navigation' : 'Open navigation');
    };

    updateScrolledState();
    buildThemeObserver();
    menu?.addEventListener('toggle', updateMenuState);
    window.addEventListener('scroll', requestScrollUpdate, { passive: true });
    window.addEventListener('resize', scheduleThemeObserver, { passive: true });
    window.addEventListener('public-navbar:refresh', buildThemeObserver);

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape' || !menu?.open) return;
        menu.open = false;
        menuToggle?.focus();
    });

    document.addEventListener('pointerdown', (event) => {
        if (menu?.open && !menu.contains(event.target)) menu.open = false;
    });
}
