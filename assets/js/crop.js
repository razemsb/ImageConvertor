document.addEventListener('DOMContentLoaded', function() {
const toggleBtn = document.getElementById('settingsToggle');
const content = document.getElementById('settingsContent');
const icon = toggleBtn.querySelector('i.fa-chevron-down');

content.style.maxHeight = '0';

toggleBtn.addEventListener('click', function () {
  if (content.style.maxHeight === '0px' || content.style.maxHeight === '') {
    content.style.maxHeight = content.scrollHeight + 'px';
    icon.classList.add('rotate-180');
    toggleBtn.classList.add('rounded-b-none');
  } else {
    content.style.maxHeight = '0';
    icon.classList.remove('rotate-180');
    toggleBtn.classList.remove('rounded-b-none');
  }
});

setTimeout(() => {
  toggleBtn.classList.add('animate-bounce');
  setTimeout(() => {
    toggleBtn.classList.remove('animate-bounce');
  }, 2000);
}, 1000);
});