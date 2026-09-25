const mainEventPage = document.querySelector('[data-main-event-page]');

if (mainEventPage) {
    const filterButtons = [...mainEventPage.querySelectorAll('[data-people-filter]')];
    const peopleCards = [...mainEventPage.querySelectorAll('[data-person-card]')];
    const resultStatus = mainEventPage.querySelector('[data-people-filter-status]');

    const applyPeopleFilter = (filter) => {
        let visibleCount = 0;

        filterButtons.forEach((button) => {
            const active = button.dataset.peopleFilter === filter;
            button.dataset.active = active ? 'true' : 'false';
            button.setAttribute('aria-pressed', active ? 'true' : 'false');
        });

        peopleCards.forEach((card) => {
            const visible = filter === 'all' || card.dataset.personCategory === filter;
            card.hidden = !visible;
            visibleCount += visible ? 1 : 0;
        });

        if (resultStatus) {
            resultStatus.textContent = `${visibleCount} people shown`;
        }
    };

    filterButtons.forEach((button) => {
        button.addEventListener('click', () => applyPeopleFilter(button.dataset.peopleFilter));
    });
}
