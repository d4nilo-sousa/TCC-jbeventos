document.addEventListener('DOMContentLoaded', () => {
  // Seleciona todos os botões com a classe 'reaction-btn'
  const buttons = document.querySelectorAll('button.reaction-btn');
  // Variável para armazenar a reação ativa ('like', 'dislike' ou null)
  let activeReaction = null;

  // Para cada botão, adiciona um listener de clique
  buttons.forEach(button => {
    button.addEventListener('click', e => {
      e.preventDefault(); // Evita o comportamento padrão do botão (ex: envio imediato)

      // Obtém o tipo da reação (like, dislike, save, notify, etc.) via data-attribute
      const type = button.dataset.type;

      // Se o tipo for 'like' ou 'dislike'
      if (type === 'like' || type === 'dislike') {
        // Se o botão clicado já estiver ativo, desativa ambos (like e dislike)
        if (activeReaction === type) {
          // Desmarca todos os botões 'like' e 'dislike'
          buttons.forEach(btn => {
            if (btn.dataset.type === 'like' || btn.dataset.type === 'dislike') {
              btn.style.backgroundColor = 'white';
              btn.style.color = '#2563eb';
            }
          });
          activeReaction = null; // Nenhuma reação ativa
        } else {
          // Se não estiver ativo, primeiro desmarca ambos os botões
          buttons.forEach(btn => {
            if (btn.dataset.type === 'like' || btn.dataset.type === 'dislike') {
              btn.style.backgroundColor = 'white';
              btn.style.color = '#2563eb';
            }
          });
          // Depois ativa o botão clicado
          button.style.backgroundColor = '#2563eb';
          button.style.color = 'white';
          activeReaction = type; // Atualiza a reação ativa
        }
      } else {
        // Para outros tipos (ex: 'save' e 'notify'), faz toggle simples de cor
        if (button.style.backgroundColor === 'rgb(37, 99, 235)') {
          button.style.backgroundColor = 'white';
          button.style.color = '#2563eb';
        } else {
          button.style.backgroundColor = '#2563eb';
          button.style.color = 'white';
        }
      }

      // Submete o formulário pai mais próximo do botão clicado
      button.closest('form').submit();
    });
  });
});
