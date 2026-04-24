document.addEventListener('DOMContentLoaded', function() {
    // Llamar al contador invisible
    fetch('contador.php').catch(e => console.warn('Contador no registrado:', e));

    // Función para inicializar carruseles
    function initCarrusel(trackSelector, btnPrevSelector, btnNextSelector, itemSelector) {
        const track = document.querySelector(trackSelector);
        if (!track) return;
        const items = track.querySelectorAll(itemSelector);
        if (items.length === 0) return;
        
        let index = 0;
        const total = items.length;
        
        const update = () => {
            const ancho = items[0].clientWidth;
            track.style.transform = `translateX(-${index * ancho}px)`;
        };

        const btnPrev = document.querySelector(btnPrevSelector);
        const btnNext = document.querySelector(btnNextSelector);
        if (btnPrev) btnPrev.addEventListener('click', () => {
            index = (index - 1 + total) % total;
            update();
        });
        if (btnNext) btnNext.addEventListener('click', () => {
            index = (index + 1) % total;
            update();
        });
        
        window.addEventListener('resize', update);
        update();
    }

    // Inicializar ambos carruseles
    initCarrusel('.carrusel-track', '.carrusel-btn.prev[data-carrusel="img"]', '.carrusel-btn.next[data-carrusel="img"]', 'img');
    initCarrusel('.carrusel-videos-track', '.carrusel-btn.prev[data-carrusel="vid"]', '.carrusel-btn.next[data-carrusel="vid"]', 'video');
});