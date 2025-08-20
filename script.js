// ==============================
// script.js — versión robusta
// Filtra SOLO la tabla "Artículos por Sede" por la columna SEDE.
// ==============================

// ---------------- Sidebar: mantener sección activa ----------------
const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a');
allSideMenu.forEach(item => {
    const li = item.parentElement;
    item.addEventListener('click', function () {
        allSideMenu.forEach(i => i.parentElement.classList.remove('active'));
        li.classList.add('active');
    });
});

// ---------------- Funciones de acciones ----------------
function completar() { alert("Opción Completar seleccionada."); }
function eliminar()   { alert("Opción Eliminar seleccionada."); }

// ---------------- Helper: visibilidad ----------------
function isVisible(el) {
    if (!el) return false;
    if (el.offsetParent === null) return false;
    const style = window.getComputedStyle(el);
    return style.visibility !== 'hidden' && style.display !== 'none' && parseFloat(style.opacity || 1) > 0;
}

// ---------------- Utility: debounce ----------------
function debounce(fn, ms = 200) {
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn.apply(null, args), ms); };
}

// ---------------- Detectores robustos ----------------
function locateTopSearchInput() {
    // 1) buscar inputs dentro de header/nav
    const header = document.querySelector('header, nav, .topbar, .navbar') || document;
    let candidates = Array.from(header.querySelectorAll('input'))
        .filter(i => isVisible(i) && (i.type === 'search' || /buscar|search|categoria|categoria/i.test(i.placeholder || i.name || i.id || '')));
    if (candidates.length) return { input: candidates[0], container: header };

    // 2) buscar inputs visibles muy arriba en la página (cerca del top)
    candidates = Array.from(document.querySelectorAll('input')).filter(i => {
        try {
            const r = i.getBoundingClientRect();
            return isVisible(i) && r.top >= 0 && r.top < (window.innerHeight * 0.22) && r.width > 80;
        } catch (e) { return false; }
    });
    if (candidates.length) return { input: candidates[0], container: candidates[0].closest('header, nav, .topbar, .searchbar') || document };

    // 3) fallback: cualquier input visible con placeholder 'Buscar'
    const any = Array.from(document.querySelectorAll('input')).find(i => isVisible(i) && /buscar|search/i.test(i.placeholder || ''));
    if (any) return { input: any, container: any.closest('header, nav, .topbar') || document };

    return { input: null, container: document };
}

function locateTopSearchButton(container, input) {
    if (!container) container = document;
    // buscar botón en el contenedor cercano al input
    if (input) {
        const parent = input.parentElement;
        const btn = parent && (parent.querySelector('button, .lupa, .search-btn, .btn-search, .search-icon') ||
            parent.nextElementSibling && parent.nextElementSibling.querySelector && parent.nextElementSibling.querySelector('button'));
        if (btn && isVisible(btn)) return btn;
    }
    // fallback: buscar icono/lupa cerca del top
    const candidates = Array.from(container.querySelectorAll('button, a')).filter(el => /search|buscar|lupa|icon-search/i.test(el.className + ' ' + (el.innerText || '')));
    return candidates[0] || null;
}

// ---------------- Localizar tabla "Artículos por Sede" ----------------
function getTablaArticulosPorSede() {
    // 1) Buscar encabezados con texto parecido
    const headings = Array.from(document.querySelectorAll('h1,h2,h3,h4,h5,.card-title,.title,.panel-title'));
    const heading = headings.find(h => /art[íi]culos?\s*por\s*sede|articulos?\s*por\s*sede|por\s*sede/i.test((h.textContent || '').trim()));
    if (heading) {
        const scope = heading.closest('section, .card, .panel, .container, div') || heading.parentElement;
        if (scope) {
            const table = scope.querySelector('table');
            if (table) return table;
            // mirar hermanos por si la tabla está al lado
            let sib = scope.nextElementSibling;
            for (let i = 0; i < 6 && sib; i++) {
                const t2 = sib.querySelector?.('table');
                if (t2) return t2;
                sib = sib.nextElementSibling;
            }
        }
    }

    // 2) Buscar tabla con encabezado 'Sede' y 'Elemento'
    const tables = Array.from(document.querySelectorAll('table'));
    for (const t of tables) {
        const ths = Array.from(t.querySelectorAll('thead th')).map(th => (th.textContent || '').trim().toLowerCase());
        if (ths.length >= 2 && ths[0].includes('sede') && ths[1].includes('elemento')) return t;
    }

    // 3) fallback: devolver la tabla más a la derecha (mayor x centro)
    if (tables.length) {
        let best = tables[0], bestX = 0;
        tables.forEach(t => {
            try {
                const r = t.getBoundingClientRect();
                const cx = r.left + r.width / 2;
                if (cx > bestX) { bestX = cx; best = t; }
            } catch (e) {}
        });
        return best;
    }
    return null;
}

// ---------------- Filtrado (por SEDE) ----------------
function filtrarTablaArticulosPorSede(valor) {
    const filtro = (valor || '').trim().toLowerCase();
    const tabla = getTablaArticulosPorSede();
    console.log('[Filtro Sede] valor ->', filtro, 'tabla encontrada?', !!tabla);
    if (!tabla) return;

    // identificar índice columna "Sede"
    let idxSede = 0;
    const ths = Array.from(tabla.querySelectorAll('thead th'));
    const found = ths.findIndex(th => /sede/i.test(th.textContent || ''));
    if (found >= 0) idxSede = found;

    const filas = Array.from(tabla.querySelectorAll('tbody tr')).filter(r => !r.classList.contains('no-results-row'));
    let visibles = 0;
    filas.forEach(row => {
        const celdas = row.children;
        const textoSede = (celdas[idxSede]?.innerText || celdas[0]?.innerText || '').trim().toLowerCase();
        const mostrar = (filtro === '' || textoSede.includes(filtro));
        row.style.display = mostrar ? '' : 'none';
        if (mostrar) visibles++;
    });

    // fila "no results"
    const tbody = tabla.querySelector('tbody');
    let vacia = tbody.querySelector('.no-results-row');
    if (visibles === 0) {
        if (!vacia) {
            vacia = document.createElement('tr');
            vacia.className = 'no-results-row';
            const td = document.createElement('td');
            td.colSpan = ths.length || (tabla.querySelectorAll('tbody tr:first-child td').length || 4);
            td.textContent = 'No se encontraron artículos para esa sede.';
            vacia.appendChild(td);
            tbody.appendChild(vacia);
        } else {
            vacia.style.display = '';
        }
    } else if (vacia) {
        vacia.style.display = 'none';
    }
}

// ---------------- Lógica principal y captura de submit ----------------
document.addEventListener('DOMContentLoaded', function () {
    // localizar input/boton superior robustamente
    const top = locateTopSearchInput();
    const topSearchInput = top.input;
    const topSearchContainer = top.container;
    const topSearchButton = locateTopSearchButton(topSearchContainer, topSearchInput);

    console.log('[Debug] topSearchInput?', topSearchInput, 'button?', topSearchButton, 'container?', topSearchContainer);

    // función que detiene la navegación/submit y filtra
    function interceptAndFilter(e, q) {
        if (e && typeof e.preventDefault === 'function') {
            e.preventDefault();
            e.stopPropagation && e.stopPropagation();
        }
        const value = (q !== undefined) ? q : (topSearchInput ? topSearchInput.value : '');
        filtrarTablaArticulosPorSede(value);
    }

    // Interceptar submit en cualquier form que contenga el input superior (capturing)
    if (topSearchInput) {
        const form = topSearchInput.closest('form');
        if (form) {
            form.addEventListener('submit', function (e) {
                console.log('[Debug] interceptando submit en form del buscador superior');
                interceptAndFilter(e);
            }, true);
        }
    }

    // Interceptar cualquier submit dentro del header/top container (capturing)
    document.addEventListener('submit', function (e) {
        try {
            if (topSearchContainer && topSearchContainer.contains(e.target)) {
                console.log('[Debug] interceptando submit dentro de container del top search');
                interceptAndFilter(e);
            }
        } catch (err) { /* no romper */ }
    }, true);

    // Click en la lupa / botón
    if (topSearchButton) {
        topSearchButton.addEventListener('click', function (e) {
            console.log('[Debug] click en boton lupa');
            interceptAndFilter(e);
        }, { capture: true });
    }

    // Enter dentro del campo
    if (topSearchInput) {
        topSearchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                console.log('[Debug] Enter en input topSearchInput');
                interceptAndFilter(e);
            }
        });
        // filtrado en vivo (debounce)
        topSearchInput.addEventListener('input', debounce(function () {
            interceptAndFilter(null);
        }, 220));
    }

    // También soporte para tu dropdown interno (si lo usas)
    const buscarBtn = document.getElementById('buscar-btn');
    const inputSede = document.getElementById('search-sede');
    if (buscarBtn) buscarBtn.addEventListener('click', (e) => { e.preventDefault(); filtrarTablaArticulosPorSede(inputSede?.value || ''); });

    // Observador por si la tabla aparece o cambia después de cargar (AJAX/PHP)
    const mo = new MutationObserver(debounce((mutationsList) => {
        const tabla = getTablaArticulosPorSede();
        if (tabla) {
            console.log('[Debug] tabla Artículos por Sede detectada por MutationObserver:', tabla);
            // aplicar filtro actual si hay texto
            const current = topSearchInput ? topSearchInput.value : (inputSede ? inputSede.value : '');
            if (current) filtrarTablaArticulosPorSede(current);
        }
    }, 300));
    mo.observe(document.body, { childList: true, subtree: true });

    // Mensaje inicial de ayuda en consola
    console.log('%c[Helper] Si filtrar por sede no funciona, revisa aquí: ', 'color: #0a7; font-weight: bold;');
    console.log(' - topSearchInput encontrado:', topSearchInput);
    console.log(' - topSearchButton  encontrado:', topSearchButton);
    console.log(' - Tabla "Artículos por Sede" (si detectada):', getTablaArticulosPorSede());
});
