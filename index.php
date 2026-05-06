<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Fibrasan</title>

    <link rel="icon" type="image/x-icon" href="/google/public/favicon.ico">

    <link rel="stylesheet" href="./css/layout.css" />
    <link rel="stylesheet" href="./css/historia/historia.css" />
    <link rel="stylesheet" href="./css/politica/politica.css" />
    <link rel="stylesheet" href="./css/catalogo/catalogo.css" />
    <link rel="stylesheet" href="./css/pedidos/pedidos.css" />

    <script src="/google/js/preguntas.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet" />
</head>

<body>
    <div id="app"></div>

    <script>
    // 🔥 Solo rutas HTML (NO PHP aquí)
    const ROUTES = {
        "home": "templates/home.php",
        "historia": "templates/historia.php",
        "politica_de_calidad": "templates/politica_de_calidad.php",
        "catalogo": "templates/catalogo.php",
        "pedidos": "templates/pedidos.php",
        "preguntas_frecuentes": "templates/preguntas_frecuentes.php",
        "trabaja_con_nosotros": "templates/trabaja_con_nosotros.php",
    };

    async function loadView(href) {
        if (!href) {
            console.error("Ruta no definida");
            return;
        }

        const content = await fetch(href).then(r => r.text());
        const contentDiv = document.getElementById("content");

        if (!contentDiv) {
            console.error("❌ #content no existe aún");
            return;
        }

        contentDiv.innerHTML = content;

        // 🔥 FIX aquí
        if (href && href.includes('pedidos')) {
            const form = document.getElementById('pedidoForm');

            if (form) {
                form.onsubmit = async function(e) {
                    e.preventDefault();

                    const btn = document.getElementById('submitBtn');
                    const btnText = document.getElementById('btnText');
                    const loader = document.getElementById('loader');

                    btn.disabled = true;
                    loader.classList.remove('hidden');
                    btnText.textContent = 'Enviando...';

                    try {
                        const res = await fetch('templates/pedidos.php', {
                            method: 'POST',
                            body: new FormData(form)
                        });

                        const data = await res.json();

                        if (data.success) {
                            document.getElementById('content').innerHTML =
                                `<div style="background:#fff;border:1px solid #e8e4dc;border-radius:16px;padding:48px;text-align:center;max-width:896px;margin:40px auto">
                            <h2 style="font-size:24px;color:#1a1a1a;font-weight:300;margin-bottom:12px">
                                Solicitud enviada correctamente
                            </h2>
                            <p style="color:#666;font-size:15px">
                                Te contactaremos pronto con disponibilidad y cotización.
                            </p>
                        </div>`;
                        } else {
                            throw new Error(data.error || 'Error');
                        }

                    } catch (err) {
                        alert(err.message);
                        btn.disabled = false;
                        loader.classList.add('hidden');
                        btnText.textContent = 'Enviar solicitud';
                    }
                };
            }
        }
    }

    function getTemplateFromURL() {
        const path = window.location.pathname.replace('/google/', '');
        return path || 'home';
    }

    async function loadPage() {
        const layout = await fetch("layouts/encabezados.php").then(r => r.text());
        document.getElementById("app").innerHTML = layout;

        const route = getTemplateFromURL();
        const view = ROUTES[route] || ROUTES['home'];

        await loadView(view);

        document.querySelectorAll("#nav-links a").forEach(link => {
            link.addEventListener("click", async (e) => {
                e.preventDefault();
                let route = link.getAttribute("data-route");
                if (!route) {
                    route = link.getAttribute("href");
                }
                const view = ROUTES[route] || ROUTES['home'];
                history.pushState({}, "", route);
                await loadView(view);
            });
        });
    }

    window.addEventListener("hashchange", async () => {
        await loadView(getTemplateFromURL());
    });

    loadPage();
    </script>
</body>

</html>