document.addEventListener('click', function(e) {
  const question = e.target.closest('.faq-question');
  if (!question) return;

  const answer = question.nextElementSibling;
  const isOpen = !answer.classList.contains('hidden');

  // cerrar todos
  document.querySelectorAll('.faq-answer').forEach(ans => {
    ans.classList.add('hidden');
  });

  document.querySelectorAll('.faq-question span:last-child').forEach(icon => {
    icon.style.transform = 'rotate(0deg)';
  });

  // toggle actual
  if (!isOpen) {
    answer.classList.remove('hidden');
    question.querySelector('span:last-child').style.transform = 'rotate(90deg)';
  }
});
