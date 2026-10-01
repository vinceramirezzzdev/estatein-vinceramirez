/* --------------------------------------------------------------------------
   Forms — inline validation + submission.

   WordPress renders each form with data-endpoint (admin-ajax.php) and a nonce,
   so it is sent with fetch and the reply is shown inline. Without JavaScript
   the same form posts to admin-post.php. The static preview has no backend,
   so it only validates and says so.
   -------------------------------------------------------------------------- */
const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

function fieldIsValid(field) {
  if (field.type === 'checkbox') return !field.required || field.checked;
  const value = field.value.trim();
  if (field.required && !value) return false;
  if (field.type === 'email' && value && !EMAIL_RE.test(value)) return false;
  return true;
}

function setStatus(form, message, state) {
  const status = form.querySelector('.form-status');
  if (!status) return;
  status.textContent = message;
  status.dataset.state = state || '';
}

function initForms() {
  document.querySelectorAll('form[data-form]').forEach(form => {
    const fields = Array.from(form.querySelectorAll('input, select, textarea')).filter(f => f.type !== 'hidden');

    fields.forEach(field => {
      const clear = () => {
        if (field.getAttribute('aria-invalid') === 'true' && fieldIsValid(field)) field.removeAttribute('aria-invalid');
      };
      field.addEventListener('input', clear);
      field.addEventListener('change', clear);
    });

    form.addEventListener('submit', async event => {
      event.preventDefault();

      const invalid = fields.filter(field => !fieldIsValid(field));
      fields.forEach(field => field.removeAttribute('aria-invalid'));
      invalid.forEach(field => field.setAttribute('aria-invalid', 'true'));
      if (invalid.length) {
        const agree = invalid.find(f => f.type === 'checkbox');
        setStatus(form, agree && invalid.length === 1
          ? 'Please agree to the Terms of Use and Privacy Policy.'
          : 'Please check the highlighted fields.', 'error');
        invalid[0].focus();
        return;
      }

      const endpoint = form.dataset.endpoint;
      if (!endpoint) {
        setStatus(form, 'Thanks! This static preview does not send messages — the WordPress site delivers them to the Estatein team.', 'success');
        form.reset();
        return;
      }

      const submit = form.querySelector('[type="submit"]');
      if (submit) submit.disabled = true;
      setStatus(form, 'Sending…');
      try {
        const response = await fetch(endpoint, { method: 'POST', body: new FormData(form), credentials: 'same-origin' });
        const json = await response.json();
        if (!json.success) {
          // Highlight the fields the server rejected.
          ((json.data && json.data.fields) || []).forEach(name => {
            const field = form.elements[name];
            if (field && field.setAttribute) field.setAttribute('aria-invalid', 'true');
          });
          throw new Error((json.data && json.data.message) || 'Something went wrong.');
        }
        setStatus(form, json.data.message, 'success');
        form.reset();
      } catch (error) {
        setStatus(form, error.message || 'Something went wrong. Please try again.', 'error');
      } finally {
        if (submit) submit.disabled = false;
      }
    });
  });
}
