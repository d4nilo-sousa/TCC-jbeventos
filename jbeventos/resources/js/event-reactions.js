document.addEventListener('DOMContentLoaded', () => {
  // Seleciona todos os botões com a classe 'reaction-btn'
  const buttons = document.querySelectorAll('button.reaction-btn');

  buttons.forEach(button => {
    // Adiciona listener de clique para cada botão
    button.addEventListener('click', e => {
      e.preventDefault(); // Previne comportamento padrão do botão (submit)

      // Obtém o tipo de reação do atributo data-type do botão
      const type = button.dataset.type;
      // Busca o formulário pai mais próximo do botão
      const form = button.closest('form');
      // Cria um FormData com os dados do formulário
      const formData = new FormData(form);

      // Verifica se o botão já está ativo (tem a classe bg-blue-600)
      const isActive = button.classList.contains('bg-blue-600');

      // Define grupos mutuamente exclusivos (like <-> dislike)
      const mutuallyExclusive = {
        like: 'dislike',
        dislike: 'like',
      };

      // --- Estilo visual: toggle atual ---
      if (isActive) {
        // Se ativo, desmarca o botão atual (remove cor azul, adiciona branco)
        button.classList.remove('bg-blue-600', 'text-white');
        button.classList.add('bg-white', 'text-blue-600');
      } else {
        // Se não ativo, marca o botão atual (cor azul)
        button.classList.add('bg-blue-600', 'text-white');
        button.classList.remove('bg-white', 'text-blue-600');

        // Se for 'like' ou 'dislike', desmarca o botão oposto
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

      // --- Envio por fetch ---
      fetch(form.action, {
        method: form.method,
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => response.json())
      .then(data => {
        // Caso receba erro indicando que telefone é necessário, alerta e redireciona
        if (data.error === 'phone_required') {
          alert('Você precisa cadastrar seu número de celular para ser notificado.');
          window.location.href = '/phone/edit'; // ajuste essa URL se precisar
        }
      })
      .catch(error => {
        // Loga erro no console caso falhe o fetch
        console.error('Erro ao processar a reação:', error);
      });
    });
  });
});
