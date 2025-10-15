document.addEventListener('DOMContentLoaded', () => {
  const res = document.getElementById('result');

  // GET: buscar por código (get.html)
  const getForm = document.querySelector('form[action="../srv/get_order.php"]');
  if (getForm && res) {
    getForm.addEventListener('submit', async e => {
      e.preventDefault();
      const code = getForm.elements.code.value.trim();
      const r = await fetch(`../srv/get_order.php?code=${encodeURIComponent(code)}`);
      res.style.display = 'block'; res.innerHTML = `<pre>${await r.text()}</pre>`;
    });
  }

  // CREATE: habilitar qty al marcar producto + enviar por POST (create.html)
  const createForm = document.getElementById('orderForm');
  if (createForm && res) {
    createForm.querySelectorAll('fieldset input[type="checkbox"]').forEach(chk=>{
      const qty=chk.parentElement.querySelector('input[type="number"]');
      chk.addEventListener('change',()=>{ qty.disabled=!chk.checked; if(!chk.checked) qty.value=''; });
    });
    createForm.addEventListener('submit', async e => {
      e.preventDefault();
      const r = await fetch(createForm.action,{method:'POST',body:new FormData(createForm)});
      res.style.display='block'; res.innerHTML = `<pre>${await r.text()}</pre>`;
    });
  }
});
