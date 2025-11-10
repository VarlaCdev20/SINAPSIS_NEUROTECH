document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('formContacto');
  const fields = {
    nombre: {
      el: document.getElementById('nombre'),
      min: 3,
      pattern: /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s'-]+$/,
      msg: "El nombre solo debe contener letras y mínimo 3 letras."
    },
    ap_paterno: {
      el: document.getElementById('ap_paterno'),
      min: 4,
      pattern: /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s'-]+$/,
      msg: "El apellido paterno solo debe contener letras y mínimo 4 letras."
    },
    ap_materno: {
      el: document.getElementById('ap_materno'),
      min: 4,
      pattern: /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s'-]+$/,
      msg: "El apellido materno solo debe contener letras y mínimo 4 letras."
    },
    fecha_nacimiento: {
      el: document.getElementById('fecha_nacimiento'),
      validate: () => {
        const v = fields.fecha_nacimiento.el.value;
        if (!v) return "Fecha obligatoria.";
        const birth = new Date(v + "T00:00:00");
        const today = new Date();
        const minDate = new Date(today.getFullYear() - 90, today.getMonth(), today
          .getDate());
        const maxDate = new Date(today.getFullYear() - 18, today.getMonth(), today
          .getDate());
        if (birth < minDate) return "La edad no puede ser mayor a 90 años.";
        if (birth > maxDate) return "Debes tener al menos 18 años.";
        return "";
      }
    },
    direccion: {
      el: document.getElementById('direccion'),
      min: 10,
      msg: "La dirección debe tener al menos 10 caracteres."
    },
    ci: {
      el: document.getElementById('ci'),
      min: 8,
      max: 10,
      pattern: /^[0-9]+$/,
      msg: "La cédula debe contener solo números con longitud entre 8 a 10."
    },
    celular: {
      el: document.getElementById('celular'),
      min: 8,
      max: 11,
      pattern: /^[0-9]+$/,
      msg: "El celular debe contener solo números con longitud entre 8 a 11."
    },
    correo: {
      el: document.getElementById('correo'),
      pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
      msg: "Formato de correo inválido."
    },
    descripcion: {
      el: document.getElementById('descripcion'),
      min: 20,
      msg: "Describe con mínimo 10 caracteres explicando tu situación para poder ayudarte.",
      required: true
    }
  };
  function setError(field, msg) {
    clearError(field);
    field.classList.remove('valid');
    field.classList.add('input-error');
    const div = document.createElement('div');
    div.className = 'error-message';
    div.textContent = msg;
    field.insertAdjacentElement('afterend', div);
  }
  function setValid(field) {
    clearError(field);
    field.classList.add('valid');
  }
  function clearError(field) {
    field.classList.remove('input-error');
    field.classList.remove('valid');
    const next = field.nextElementSibling;
    if (next && next.classList.contains('error-message')) next.remove();
  }
  function validate(fieldObj) {
    const el = fieldObj.el;
    const v = el.value.trim();
    if (fieldObj.required && !v) {
      setError(el, "Este campo es obligatorio.");
      return false;
    }
    if (fieldObj.min && v.length < fieldObj.min) {
      setError(el, fieldObj.msg);
      return false;
    }
    if (fieldObj.max && v.length > fieldObj.max) {
      setError(el, fieldObj.msg);
      return false;
    }
    if (fieldObj.pattern && !fieldObj.pattern.test(v)) {
      setError(el, fieldObj.msg);
      return false;
    }
    if (fieldObj.validate) {
      const msg = fieldObj.validate();
      if (msg !== "") {
        setError(el, msg);
        return false;
      }
    }
    setValid(el);
    return true;
  }
  Object.values(fields).forEach(f => {
    f.el.addEventListener(f.el.type === "date" ? "change" : "input", () => validate(f));
  });
  form.addEventListener('submit', (e) => {
    let ok = true;
    Object.values(fields).forEach(f => {
      if (!validate(f)) ok = false;
    });
    if (!ok) e.preventDefault();
  });
});
