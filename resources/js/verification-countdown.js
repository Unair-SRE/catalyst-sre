const resendForm = document.querySelector('[data-verification-resend]');

if (resendForm) {
    const button = resendForm.querySelector('button');
    const countdown = resendForm.querySelector('[data-verification-countdown]');
    const ready = resendForm.querySelector('[data-verification-ready]');
    let seconds = 60;

    const timer = window.setInterval(() => {
        seconds -= 1;
        countdown.textContent = `Resend available in ${seconds}s`;

        if (seconds === 0) {
            window.clearInterval(timer);
            button.disabled = false;
            countdown.classList.add('hidden');
            ready.classList.remove('hidden');
        }
    }, 1000);
}
