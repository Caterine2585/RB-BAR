document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('.nav-toggle');
    const navigation = document.querySelector('.nav-links');

    if (toggle && navigation) {
        toggle.addEventListener('click', () => {
            const isOpen = navigation.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', String(isOpen));
        });

        navigation.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => {
            navigation.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
        }));
    }

    const recommendation = document.querySelector('#plan-recommendation');
    document.querySelectorAll('[data-plan]').forEach((button) => {
        button.addEventListener('click', () => {
            document.querySelectorAll('[data-plan]').forEach((option) => option.classList.remove('is-selected'));
            button.classList.add('is-selected');
            recommendation.textContent = button.dataset.message;
        });
    });
});
