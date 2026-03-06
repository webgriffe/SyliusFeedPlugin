document.addEventListener('click', (e) => {
    const button = e.target.closest('[data-copy-url]');
    if (!button) return;

    const url = button.dataset.copyUrl;
    const feedbackId = button.dataset.copyFeedback;

    navigator.clipboard.writeText(url).then(() => {
        if (!feedbackId) return;

        const feedback = document.getElementById(feedbackId);
        if (!feedback) return;

        feedback.classList.remove('d-none');

        const existing = feedback._copyTimer;
        if (existing) clearTimeout(existing);

        feedback._copyTimer = setTimeout(() => {
            feedback.classList.add('d-none');
        }, 3000);
    });
});
