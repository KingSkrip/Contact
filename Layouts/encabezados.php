<?php

use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// helper opcional (recomendado)
function env($key, $default = null)
{
    return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?? $default;
}
?>

<!-- NAV -->
<nav>
    <a class="nav-brand">
        <div class="nav-logo">
            <img src="./images/logo.png" alt="">
            <circle cx="12" cy="12" r="4" stroke="#2aa8a0" stroke-width="1.8" />
            <path
                d="M12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9l2.1 2.1M16.9 16.9l2.1 2.1M19.1 4.9l-2.1 2.1M7.1 16.9l-2.1 2.1"
                stroke="#2aa8a0" stroke-width="1.5" stroke-linecap="round" />
            </svg>
        </div>
        <span class="text-fb">FIBRASAN</span>
    </a>

    <button class="hamburger" id="hamburger" aria-label="Menú">
        <span></span><span></span><span></span>
    </button>

    <ul class="nav-links" id="nav-links">
        <a href="home" data-route="home">Inicio</a>
        <a href="historia" data-route="historia">Historia</a>
        <a href="catalogo" data-route="catalogo">Catálogo</a>
        <a href="pedidos" data-route="pedidos">Pedidos</a>
        <li><a href="preguntas_frecuentes" data-route="preguntas_frecuentes">Preguntas frecuentes</a></li>
        <li><a href="trabaja_con_nosotros" data-route="trabaja_con_nosotros">Trabaja con nosotros</a></li>
        <li>
            <button class="nav-search" aria-label="Buscar">
                <svg class="vu8Pwe tCHXDc YSH9J" viewBox="0 0 24 24" focusable="false">
                    <path
                        d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z">
                    </path>
                    <path d="M0 0h24v24H0z" fill="none"></path>
                </svg>

            </button>
        </li>
    </ul>
</nav>

<main id="content"></main>

<!-- FOOTER -->
<footer>
    <a class="footer-cta hover:underline hover:text-blue-600 transition">
        Catálogo
    </a>

    <div class="footer-info">
        <!-- Correo -->
        <a href="mailto:ventas@fibrasan.com.mx" class="hover:underline hover:text-blue-600 transition">
            ventas@fibrasan.com.mx
        </a>

        <span class="footer-sep">|</span>

        <!-- Dirección -->
        <a href="https://www.google.com/maps?q=Av.+de+las+Partidas+Col.+Corredor+Industrial,+52004+Lerma+de+Villada,+México"
            target="_blank" class="hover:underline hover:text-blue-600 transition">
            Av. de las Partidas Col. Corredor Industrial, 52004 Lerma de Villada, Méx.
        </a>

        <span class="footer-sep">|</span>

        <!-- Teléfono -->
        <a href="tel:+527281193555" class="hover:underline hover:text-blue-600 transition">
            728 119 3555
        </a>

        <span class="footer-sep">|</span>

        <!-- Aviso -->
        <a href="/google/Docs/avisopriv/Aviso_de_Privacidad_Lerma.pdf" target="_blank" rel="noopener noreferrer"
            class="hover:underline hover:text-blue-600 transition">
            Aviso de privacidad
        </a>
    </div>
</footer>

<!-- layout -->