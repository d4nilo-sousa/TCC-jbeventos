document.addEventListener('DOMContentLoaded', () => {
  const buttons = document.querySelectorAll('button.reaction-btn');
  const phoneModal = document.getElementById('phoneModal');
  const cancelPhoneModal = document.getElementById('cancelPhoneModal');
  const phoneForm = document.getElementById('phoneForm');
  const phoneInput = phoneForm.querySelector('input[name="phone_number"]');

  let pendingReaction = null;

  // Máscara simples para telefone (formato (99) 99999-9999)
  function maskPhone(value) {
    value = value.replace(/\D/g, '');

    if (value.length > 11) {
      value = value.slice(0, 11);
    }

    if (value.length > 6) {
      return `(${value.slice(0,2)}) ${value.slice(2,7)}-${value.slice(7,11)}`;
    } else if (value.length > 2) {
      return `(${value.slice(0,2)}) ${value.slice(2)}`;
    } else if (value.length > 0) {
      return `(${value}`;
    }
    return '';
  }

  phoneInput.addEventListener('input', e => {
    const cursorPosition = phoneInput.selectionStart;
    const originalLength = phoneInput.value.length;

    phoneInput.value = maskPhone(phoneInput.value);

    const newLength = phoneInput.value.length;
    const diff = newLength - originalLength;

    phoneInput.selectionStart = phoneInput.selectionEnd = cursorPosition + diff;
  });

  buttons.forEach(button => {
    button.addEventListener('click', e => {
      e.preventDefault();

      const type = button.dataset.type;
      const form = button.closest('form');
      const formData = new FormData(form);

      const isActive = button.classList.contains('bg-blue-600');

      const mutuallyExclusive = {
        like: 'dislike',
        dislike: 'like',
      };

      // Toggle visual
      if (isActive) {
        button.classList.remove('bg-blue-600', 'text-white');
        button.classList.add('bg-white', 'text-blue-600');
      } else {
        button.classList.add('bg-blue-600', 'text-white');
        button.classList.remove('bg-white', 'text-blue-600');

        if (mutuallyExclusive[type]) {
          const opposite = mutuallyExclusive[type];
          buttons.forEach(btn => {
            if (btn.dataset.type === opposite) {
              btn.classList.remove('bg-blue-600', 'text-white');
              btn.classList.add('bg-white', 'text-blue-600');
            }
          });
        }
      }

      fetch(form.action, {
        method: form.method,
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => {
        if (!response.ok) {
          return response.json().then(data => { throw data; });
        }
        return response.json();
      })
      .then(data => {
        // Toast para reações notify adicionadas ou removidas
        if (data.type === 'notify') {
          if (data.status === 'added') {
            showToast('🔔 Você receberá notificações deste evento!');
          } else if (data.status === 'removed') {
            showToast('🚫 Você não receberá mais notificações deste evento.');
          }
        }
      })
      .catch(data => {
        if (data.error === 'phone_required' && !isActive && type === 'notify') {
          pendingReaction = { form, formData };
          phoneModal.classList.remove('hidden');
        } else {
          console.error('Erro ao processar a reação:', data);
        }
      });
    });
  });

  cancelPhoneModal.addEventListener('click', () => {
    phoneModal.classList.add('hidden');
  });

  phoneForm.addEventListener('submit', e => {
    e.preventDefault();

    const formData = new FormData(phoneForm);
    
    // Pega o token CSRF do meta tag, já que ele pode não estar no formData
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Adiciona o token CSRF ao formData para a requisição
    if (!formData.get('_token')) {
      formData.append('_token', csrfToken);
    }

    // Adiciona o método PUT ao formData para simular a requisição
    if (!formData.get('_method')) {
      formData.append('_method', 'PUT');
    }

    fetch(phoneForm.action, {
      method: 'POST', // O método precisa ser POST para enviar o _method
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken
      }
    })
    .then(response => {
      if (!response.ok) {
        return response.json().then(data => { throw data; });
      }
      return response.json();
    })
    .then(data => {
      if (data.success) {
        phoneModal.classList.add('hidden');
        showToast('📱 Telefone atualizado com sucesso! Você receberá notificações deste evento!');

        if (pendingReaction) {
          fetch(pendingReaction.form.action, {
            method: pendingReaction.form.method,
            body: pendingReaction.formData,
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': csrfToken
            }
          });
          pendingReaction = null;
        }
      } else {
        alert('Erro ao salvar o telefone.');
      }
    })
    .catch(error => {
      alert('Erro ao salvar o telefone.');
      console.error('Erro ao salvar telefone:', error);
    });
  });

  function showToast(message) {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');

    toastMessage.textContent = message;
    toast.classList.remove('hidden');

    setTimeout(() => {
      toast.classList.add('hidden');
    }, 3000);
  }
});