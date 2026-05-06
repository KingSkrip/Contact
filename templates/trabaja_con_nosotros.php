<?php
require __DIR__ . '/../bootstrap.php';
?>
<div class="trabaja-page">
    <!-- HERO -->
    <div class="pt-24 pb-16">
        <div class="max-w-5xl mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-6xl font-bold text-[#00698f] mb-10 leading-tight"
                style="font-family: var(--font-head)">
                Únete al equipo
            </h1>

            <!-- Card -->
            <div class="max-w-3xl mx-auto">
                <!-- Botón estilo FAQ -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                    <button
                        class="faq-question w-full text-left px-8 py-6 flex justify-between items-center hover:bg-gray-50 transition">
                        <span class="text-xl font-semibold text-[#00698f]" style="font-family: var(--font-head)">
                            Bolsa de Trabajo
                        </span>
                        <span class="text-2xl text-gray-400 transition-transform">›</span>
                    </button>

                    <div class="faq-answer hidden px-8 pb-6 text-gray-700 leading-relaxed">
                        <p class="mb-4">
                            En Fibrasán creemos que nuestro mayor valor está en las personas.
                            Buscamos talento comprometido, creativo y apasionado por la
                            industria textil.
                        </p>
                        <p class="mb-4">
                            Si quieres crecer con nosotros y ser parte de un equipo que
                            transforma ideas en proyectos de calidad, explora nuestras
                            vacantes y postúlate.
                        </p>
                        <p class="font-semibold text-[#00698f]">
                            Tu futuro comienza aquí, con Fibrasán.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CONTACTO -->
    <div class="py-16">
        <div class="max-w-2xl mx-auto text-center px-6">
            <div class="flex justify-center gap-8 mb-10">
                <!-- WhatsApp -->
                <a href="https://wa.me/<?= $_ENV['WH_TRABAJA_US'] ?>" target="_blank"
                    class="bg-[#25D366] w-16 h-16 flex items-center justify-center rounded-2xl shadow-md hover:scale-110 transition">
                    <img src="https://cdn-icons-png.flaticon.com/512/733/733585.png" class="w-8 h-8" alt="WhatsApp" />
                </a>

                <!-- Facebook -->
                <a href="<?= $_ENV['FB_TRABAJA_US'] ?>" target="_blank"
                    class="bg-[#1877F2] w-16 h-16 flex items-center justify-center rounded-2xl shadow-md hover:scale-110 transition">
                    <img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" class="w-8 h-8" alt="Facebook" />
                </a>
            </div>

            <p class="text-[#00698f] text-xl font-medium leading-relaxed">
                Tu oportunidad comienza con un clic.<br />
                <span class="font-bold">Contáctanos.</span>
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener("click", function(e) {
    const btn = e.target.closest(".trabaja-toggle");
    if (!btn) return;

    const content = btn.nextElementSibling;
    const isOpen = !content.classList.contains("hidden");

    // cerrar
    if (isOpen) {
        content.classList.add("hidden");
        btn.querySelector("span:last-child").style.transform = "rotate(0deg)";
    } else {
        content.classList.remove("hidden");
        btn.querySelector("span:last-child").style.transform = "rotate(90deg)";
    }
});
</script>