// Floristeria Bloom – JS mínimo para el criterio 9
// - Accede a elementos del HTML y los modifica
// - Usa funciones y eventos
// - Se comunica con PHP (fetch GET) y SOLO presenta el resultado

function show(el, html) {
  el.innerHTML = html;
  el.style.display = "block";
}

function hide(el) {
  el.style.display = "none";
}

function parseServerText(txt) {
  // El PHP devuelve texto plano con líneas "total: 60.50 €", etc.
  // Extraemos el total (con IVA) y, si podemos, otros campos para mostrar.
  const total = (txt.match(/^\s*total:\s*([0-9]+(?:\.[0-9]{1,2})?)/mi) || [])[1] || null;
  const code  = (txt.match(/^\s*code:\s*(.+)$/mi) || [])[1] || "";
  const name  = (txt.match(/^\s*name:\s*(.+)$/mi) || [])[1] || "";
  const prod  = (txt.match(/^\s*product:\s*(.+)$/mi) || [])[1] || "";
  const qty   = (txt.match(/^\s*qty:\s*(.+)$/mi) || [])[1] || "";
  const price = (txt.match(/^\s*price:\s*([0-9]+(?:\.[0-9]{1,2})?)/mi) || [])[1] || "";

  return { total, code, name, prod, qty, price };
}

document.addEventListener("DOMContentLoaded", () => {
  // Solo actuamos en la página de "Get Order"
  const form = document.querySelector('form[action="../srv/get_order.php"]');
  const input = form?.elements?.code;
  const result = document.getElementById("result");

  if (!form || !input || !result) return;

  input.focus();

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const code = input.value.trim();
    if (!code) {
      show(result, `<p>Please enter an order code.</p>`);
      return;
    }

    hide(result);

    try {
      const res = await fetch(`../srv/get_order.php?code=${encodeURIComponent(code)}`, {
        method: "GET",
        headers: { "Accept": "text/plain" }
      });

      const txt = await res.text();

      if (res.status === 200) {
        const data = parseServerText(txt);

        if (data.total) {
          show(
            result,
            `
            <h3>Order ${data.code || ""}</h3>
            <p><strong>Total (VAT included):</strong> € ${data.total}</p>
            <p>
              ${data.name ? `Customer: ${data.name}<br>` : ""}
              ${data.prod ? `Flower: ${data.prod}<br>` : ""}
              ${data.qty  ? `Qty: ${data.qty}<br>` : ""}
              ${data.price? `Unit price: € ${data.price}<br>` : ""}
            </p>
            `
          );
        } else {
          // Si no pudiéramos extraer el total, mostramos el texto tal cual
          show(result, `<pre>${txt}</pre>`);
        }
      } else if (res.status === 404) {
        show(result, `<p>Order not found: <code>${code}</code></p>`);
      } else if (res.status === 400) {
        show(result, `<p>Missing or invalid parameter.</p>`);
      } else {
        show(result, `<pre>${txt}</pre>`);
      }
    } catch (err) {
      show(result, `<p>Network error. Please try again.</p>`);
    }
  });
});
