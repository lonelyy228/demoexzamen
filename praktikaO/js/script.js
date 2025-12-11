document.addEventListener('DOMContentLoaded', function() {
  const buttons = document.querySelectorAll('button');
  buttons.forEach(button => {
    button.addEventListener('mouseover', function() {
      this.style.backgroundColor = 'red';
    });
    button.addEventListener('mouseout', function() {
      this.style.backgroundColor = '';
    });
  });
});