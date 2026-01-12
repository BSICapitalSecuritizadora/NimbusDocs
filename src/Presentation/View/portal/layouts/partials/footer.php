<footer class="mt-auto py-4 bg-white border-top">
    <div class="container-xxl">
        <div class="row align-items-center gy-3">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0 text-secondary small fw-medium">
                    &copy; <?= date('Y') ?> BSI Capital Securitizadora S/A. Todos os direitos reservados.
                </p>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-center justify-content-md-end gap-3 align-items-center">
                    <span class="text-uppercase small fw-bold text-muted me-2" style="font-size: 0.7rem; letter-spacing: 1px;">Conecte-se:</span>
                    <a href="https://www.instagram.com/bsicapitalsec/" target="_blank" class="text-secondary text-opacity-75 text-decoration-none transition-hover scale-hover" title="Instagram">
                        <i class="bi bi-instagram fs-5"></i>
                    </a>
                    <a href="https://br.linkedin.com/company/bsi-capital-securitizadora-s-a" target="_blank" class="text-secondary text-opacity-75 text-decoration-none transition-hover scale-hover" title="LinkedIn">
                        <i class="bi bi-linkedin fs-5"></i>
                    </a>
                    <a href="https://bsicapital.com.br" target="_blank" class="text-secondary text-opacity-75 text-decoration-none transition-hover scale-hover" title="Site">
                        <i class="bi bi-globe2 fs-5"></i>
                    </a>
                    <a href="https://www.youtube.com/@BSICapitalSecuritizadora" target="_blank" class="text-secondary text-opacity-75 text-decoration-none transition-hover scale-hover" title="YouTube">
                        <i class="bi bi-youtube fs-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
    .transition-hover {
        transition: all 0.2s ease;
    }
    .transition-hover:hover {
        color: var(--nd-primary) !important;
        opacity: 1 !important;
    }
    .scale-hover:hover {
        transform: translateY(-2px);
    }
</style>