<?php
require __DIR__ . '/../bootstrap.php';
?>
<div class="catalogo-page">
    <!-- ====================== TÍTULO PRINCIPAL ====================== -->
    <div class="quienes" style="padding-top: 100px; padding-bottom: 40px">
        <div class="max-w-6xl mx-auto text-center px-6">
            <h1 class="text-4xl lg:text-5xl font-bold text-[#00698f] mb-6" style="font-family: var(--font-head)">
                Conoce nuestros productos.
            </h1>
            <div class="inline-block backg-primary text-white font-semibold px-8 py-3 rounded-md text-lg">
                Fabricación de productos textiles
            </div>
        </div>
    </div>

    <!-- ====================== SECCIÓN FABRICACIÓN ====================== -->
    <div class="quienes py-16">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-20">
            <!-- BLOQUE 1 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Texto -->
                <div class="text-lg leading-relaxed text-black">
                    <p>
                        En Fibrasan, transformamos fibras en soluciones textiles que
                        impulsan el éxito de nuestros clientes. Con más de 20 años de
                        experiencia en la producción de hilo, tela cruda, teñido y
                        estampado, combinamos tradición, innovación y tecnología para
                        garantizar la más alta calidad en cada proceso.
                    </p>
                </div>

                <!-- Imagen derecha -->
                <div class="flex justify-center lg:justify-end">
                    <img src="./images/catalogo1.jpg" alt="Máquina textil"
                        class="w-full max-w-[400px] h-auto rounded-lg shadow-md" />
                </div>
            </div>

            <!-- BLOQUE 2 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Imagen izquierda -->
                <div class="flex justify-center lg:justify-start order-2 lg:order-1">
                    <img src="./images/catalogo2.jpg" alt="Proceso textil"
                        class="w-full max-w-xs lg:max-w-sm rounded-xl shadow-lg" />
                </div>

                <!-- Texto derecha -->
                <div class="text-lg leading-relaxed text-black order-1 lg:order-2">
                    <p>
                        Nuestro compromiso es ofrecer productos resistentes, funcionales y
                        adaptados a las necesidades de la industria, siempre cuidando cada
                        detalle para que tu proyecto brille desde la primera puntada.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- ====================== CATÁLOGO ====================== -->
    <div class="section color-white" style="padding-top: 40px; padding-bottom: 80px">
        <div class="max-w-6xl mx-auto px-6">
            <h2 class="text-4xl font-bold text-center text-[#00698f] mb-12" style="font-family: var(--font-head)">
                Catálogo
            </h2>

            <!-- Grid de Productos -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-8">
                <div class="text-center group">
                    <div class="overflow-hidden rounded-2xl shadow-md mb-4 transition-transform group-hover:scale-105">
                        <img src="./images/pique.jpg" alt="Pique" class="w-full aspect-square object-cover" />
                    </div>
                    <p class="font-medium text-gray-800">Pique</p>
                </div>

                <div class="text-center group">
                    <div class="overflow-hidden rounded-2xl shadow-md mb-4 transition-transform group-hover:scale-105">
                        <img src="./images/lycra.jpg" alt="Lycra algodón spandex"
                            class="w-full aspect-square object-cover" />
                    </div>
                    <p class="font-medium text-gray-800">Lycra algodón spandex</p>
                </div>

                <div class="text-center group">
                    <div class="overflow-hidden rounded-2xl shadow-md mb-4 transition-transform group-hover:scale-105">
                        <img src="./images/french.jpg" alt="French terry" class="w-full aspect-square object-cover" />
                    </div>
                    <p class="font-medium text-gray-800">French terry</p>
                </div>

                <div class="text-center group">
                    <div class="overflow-hidden rounded-2xl shadow-md mb-4 transition-transform group-hover:scale-105">
                        <img src="./images/jersey.jpg" alt="Jersey" class="w-full aspect-square object-cover" />
                    </div>
                    <p class="font-medium text-gray-800">Jersey</p>
                </div>

                <div class="text-center group">
                    <div class="overflow-hidden rounded-2xl shadow-md mb-4 transition-transform group-hover:scale-105">
                        <img src="./images/felpa.jpg" alt="Felpa" class="w-full aspect-square object-cover" />
                    </div>
                    <p class="font-medium text-gray-800">Felpa</p>
                </div>

                <div class="text-center group">
                    <div class="overflow-hidden rounded-2xl shadow-md mb-4 transition-transform group-hover:scale-105">
                        <img src="./images/interlock.jpg" alt="Interlock" class="w-full aspect-square object-cover" />
                    </div>
                    <p class="font-medium text-gray-800">Interlock</p>
                </div>

                <div class="text-center group">
                    <div class="overflow-hidden rounded-2xl shadow-md mb-4 transition-transform group-hover:scale-105">
                        <img src="./images/jacquar.jpg" alt="Jacquard" class="w-full aspect-square object-cover" />
                    </div>
                    <p class="font-medium text-gray-800">Jacquard</p>
                </div>

                <div class="text-center group">
                    <div class="overflow-hidden rounded-2xl shadow-md mb-4 transition-transform group-hover:scale-105">
                        <img src="./images/rib.jpg" alt="RIB" class="w-full aspect-square object-cover" />
                    </div>
                    <p class="font-medium text-gray-800">RIB</p>
                </div>

                <div class="text-center group">
                    <div class="overflow-hidden rounded-2xl shadow-md mb-4 transition-transform group-hover:scale-105">
                        <img src="./images/linea_estampados.jpg" alt="Línea de estampados"
                            class="w-full aspect-square object-cover" />
                    </div>
                    <p class="font-medium text-gray-800">Línea de estampados</p>
                </div>
            </div>

            <!-- Botón Catálogo Completo -->
            <div class="text-center mt-14">
                <a <a href="/google/Docs/catalogos/CATALOGO_FIBRASAN_2026.pdf"
                    class="inline-block backg-primary hover:bg-[#005577] text-white font-semibold px-10 py-4 rounded-xl text-lg transition-all">
                    Catálogo completo
                </a>
            </div>
        </div>
    </div>

    <!-- ====================== FRASE FINAL ====================== -->
    <div style="background: #4fd1b5; padding: 3.5rem 1rem; text-align: center">
        <p class="max-w-3xl mx-auto text-white text-lg italic font-light">
            "Desde la fibra hasta el producto final, cuidamos cada detalle."
        </p>
    </div>

    <!-- ====================== REDES SOCIALES ====================== -->
    <div class="py-8 bg-white flex justify-center gap-6">

        <!-- Facebook -->
        <a href="<?= $_ENV['FB_CATALOGOS'] ?>" target="_blank" class="text-4xl hover:scale-110 transition-transform">
            <img src="./images/facebook.png" class="w-full max-w-[40px] aspect-square object-contain" />
        </a>

        <!-- Instagram -->
        <a href="<?= $_ENV['IG_CATALOGOS'] ?>" target="_blank" class="text-4xl hover:scale-110 transition-transform">
            <img src="./images/instagram.png" class="w-full max-w-[40px] aspect-square object-contain" />
        </a>

        <!-- WhatsApp -->
        <a href="https://wa.me/<?= $_ENV['WH_CATALOGOS'] ?>" target="_blank"
            class="text-4xl hover:scale-110 transition-transform">
            <img src="./images/whatsapp.png" class="w-full max-w-[40px] aspect-square object-contain" />
        </a>

    </div>
</div>