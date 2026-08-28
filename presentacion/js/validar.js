document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('form').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      let campos = form.querySelectorAll('[required]');
      for (let campo of campos) {
        if (!campo.value.trim()) {
          alert('Completa los campos obligatorios.');
          campo.focus(); e.preventDefault(); return;
        }
      }
    });
  });
});
