document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('[data-menu-toggle]');
    const nav = document.querySelector('[data-nav-content]');

    if (toggle && nav) {
        toggle.addEventListener('click', () => {
            const isOpen = nav.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        nav.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                nav.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
            });
        });
    }

    const heroGrid = document.querySelector('[data-hero-grid]');
    const prev = document.querySelector('[data-hero-prev]');
    const next = document.querySelector('[data-hero-next]');

    const moveHero = (direction) => {
        if (!heroGrid) return;
        const amount = heroGrid.clientWidth;
        heroGrid.scrollBy({ left: amount * direction, behavior: 'smooth' });
    };

    if (prev) prev.addEventListener('click', () => moveHero(-1));
    if (next) next.addEventListener('click', () => moveHero(1));
});
