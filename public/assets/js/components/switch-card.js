document.querySelectorAll('.switch-group').forEach(group => {
  const labels = group.querySelectorAll('.switch-label');

  labels.forEach(label => {
    label.addEventListener('click', () => {
      labels.forEach(l => l.classList.remove('selected'));
      label.classList.add('selected');
    });
  });
});
