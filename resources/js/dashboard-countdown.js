export function countdownParts(target, now = Date.now()) {
    const total = Math.max(0, Math.floor((target - now) / 1000));
    if (!Number.isFinite(total)) return ['00', '00', '00', '00'];
    return [Math.floor(total / 86400), Math.floor(total / 3600) % 24,
        Math.floor(total / 60) % 60, total % 60].map(value => String(value).padStart(2, '0'));
}

// A DOM-owned timer: Livewire removal automatically releases the interval/listener.
class CatalystCountdown extends HTMLElement {
    static observedAttributes = ['target'];
    interval = null;
    update = () => {
        const parts = countdownParts(Date.parse(this.getAttribute('target')));
        this.querySelectorAll('[data-countdown-unit]').forEach((node, index) => {
            if (node.textContent !== parts[index]) node.textContent = parts[index];
        });
        this.setAttribute('aria-label', `${parts[0]} days, ${parts[1]} hours, ${parts[2]} minutes, ${parts[3]} seconds remaining`);
    };
    sync = () => {
        clearInterval(this.interval);
        this.update();
        if (!document.hidden) this.interval = setInterval(this.update, 1000);
    };
    connectedCallback() {
        this.sync();
        document.addEventListener('visibilitychange', this.sync);
    }
    attributeChangedCallback() { if (this.isConnected) this.update(); }
    disconnectedCallback() {
        clearInterval(this.interval);
        document.removeEventListener('visibilitychange', this.sync);
    }
}

if (!customElements.get('catalyst-countdown')) customElements.define('catalyst-countdown', CatalystCountdown);
